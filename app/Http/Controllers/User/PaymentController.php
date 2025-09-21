<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Lấy thông tin giỏ hàng từ session
        $cartItems = session('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        // Tính tổng tiền
        $subtotal = 0;
        $shippingFee = 30000; // Phí vận chuyển cố định 30,000 VNĐ
        
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $totalAmount = $subtotal + $shippingFee;

        return view('user.payment.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'totalAmount', 'user'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,online_payment',
            'voucher_code' => 'nullable|string|max:50',
        ], [
            'shipping_name.required' => 'Vui lòng nhập họ tên người nhận',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại',
            'shipping_phone.max' => 'Số điện thoại không được quá 20 ký tự',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'shipping_address.max' => 'Địa chỉ không được quá 500 ký tự',
            'shipping_note.max' => 'Ghi chú không được quá 1000 ký tự',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $cartItems = session('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        DB::beginTransaction();
        
        try {
            // Tính tổng tiền
            $subtotal = 0;
            $shippingFee = 30000;
            
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            $totalAmount = $subtotal + $shippingFee;

            // Áp dụng voucher chung (nếu có)
            $discountAmount = 0;
            $voucherCode = trim((string)$request->input('voucher_code'));
            if ($voucherCode !== '') {
                $voucher = \App\Models\Voucher::lockForUpdate()->where('code', $voucherCode)->first();
                if ($voucher && $voucher->is_available) {
                    // Kiểm tra đơn tối thiểu
                    if (is_null($voucher->min_order_amount) || $subtotal >= $voucher->min_order_amount) {
                        if ($voucher->discount_type === 'percent') {
                            $discountAmount = round($subtotal * ($voucher->discount_value / 100));
                        } else {
                            $discountAmount = min($totalAmount, $voucher->discount_value);
                        }
                        $totalAmount = max(0, $totalAmount - $discountAmount);
                        // Tăng số lần đã dùng
                        $voucher->increment('used_count');
                    }
                }
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name' => $request->shipping_name,
                'customer_phone' => $request->shipping_phone,
                'customer_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
                'note' => $request->shipping_note,
                'voucher_code' => $voucherCode ?: null,
                'discount_amount' => $discountAmount,
                'total_price' => $totalAmount,
                'status' => 'pending',
            ]);

            // Tạo các order items
            Log::info('Creating order items', ['cart_items' => $cartItems]);
            
            foreach ($cartItems as $cartKey => $item) {
                // Tách product_id và variant_id từ cartKey
                $parts = explode('_', $cartKey);
                $productId = $parts[0];
                $variantId = count($parts) > 1 ? $parts[1] : null;
                
                Log::info('Processing cart item', [
                    'cartKey' => $cartKey,
                    'productId' => $productId,
                    'variantId' => $variantId,
                    'item' => $item
                ]);
                
                // Tách product_name và variant_name từ name
                $productName = $item['name'];
                $variantName = null;
                
                if ($variantId) {
                    // Nếu có variant, tách tên sản phẩm và variant
                    $nameParts = explode(' - ', $item['name']);
                    if (count($nameParts) > 1) {
                        $productName = $nameParts[0];
                        $variantName = $nameParts[1];
                    }
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'product_name' => $productName,
                    'variant_name' => $variantName,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }

            // Xóa giỏ hàng
            session()->forget('cart');

            DB::commit();

            // Chuyển hướng dựa trên phương thức thanh toán
            if ($request->payment_method === 'online_payment') {
                return redirect()->route('payment.online', $order);
            } elseif ($request->payment_method === 'bank_transfer') {
                return redirect()->route('payment.bank', $order);
            } else {
                return redirect()->route('payment.success', $order)->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment process error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng. Vui lòng thử lại.');
        }
    }

    public function onlinePayment(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return view('user.payment.online', compact('order'));
    }

    public function bankTransfer(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return view('user.payment.bank', compact('order'));
    }

    public function success(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return view('user.payment.success', compact('order'));
    }

    public function verifyPayment(Request $request, Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
        }

        // Xử lý xác minh thanh toán (có thể tích hợp với cổng thanh toán thực tế)
        // Ở đây chỉ là demo
        $paymentStatus = $request->input('status', 'pending');
        
        if ($paymentStatus === 'success') {
            $order->update(['status' => 'processing']);
            return response()->json(['success' => true, 'message' => 'Thanh toán thành công!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Thanh toán thất bại!']);
        }
    }
}
