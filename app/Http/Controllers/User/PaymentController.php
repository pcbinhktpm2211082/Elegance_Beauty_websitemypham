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
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

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
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,online_payment,stripe,vnpay',
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

            // Thông báo cho admin về đơn hàng mới
            AdminNotification::create([
                'title' => 'Đơn hàng mới ' . $order->order_code,
                'message' => 'Khách hàng ' . $order->customer_name . ' vừa đặt đơn với tổng giá trị ' . number_format($order->total_price, 0, ',', '.') . ' đ.',
                'type' => 'info',
            ]);

            DB::commit();

            // Chuyển hướng dựa trên phương thức thanh toán
            if ($request->payment_method === 'online_payment') {
                return redirect()->route('payment.online', $order);
            } elseif ($request->payment_method === 'bank_transfer') {
                $this->clearCartSession();
                return redirect()->route('payment.bank', $order);
            } elseif ($request->payment_method === 'stripe') {
                return $this->startStripePayment($order);
            } elseif ($request->payment_method === 'vnpay') {
                return $this->startVnpayPayment($order);
            } else {
                $this->clearCartSession();
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

    /**
     * Khởi tạo thanh toán Stripe và chuyển hướng đến Checkout Session.
     */
    protected function startStripePayment(Order $order)
    {
        $config = $this->stripeConfig();
        if (!$config) {
            return redirect()->route('payment.checkout')
                ->with('error', 'Chưa cấu hình thông tin Stripe. Vui lòng chọn phương thức khác.');
        }

        $secretKey = $config['secret'];
        $successUrl = route('payment.stripe.success', ['order' => $order->id]);
        $cancelUrl = route('payment.checkout');

        // Lấy order items để tạo line items
        // Stripe hỗ trợ VND (zero-decimal currency), không cần convert
        $lineItems = [];
        
        foreach ($order->orderItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'vnd', // VND là zero-decimal currency trong Stripe
                    'product_data' => [
                        'name' => $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''),
                    ],
                    'unit_amount' => (int)$item->unit_price, // VND không cần nhân 100
                ],
                'quantity' => $item->quantity,
            ];
        }

        // Thêm phí vận chuyển nếu có
        $shippingFee = 30000;
        if ($shippingFee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => [
                        'name' => 'Phí vận chuyển',
                    ],
                    'unit_amount' => (int)$shippingFee, // VND không cần nhân 100
                ],
                'quantity' => 1,
            ];
        }

        try {
            // Build form data với format đúng cho Stripe API
            $formData = [
                'payment_method_types[]' => 'card',
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'metadata[order_id]' => $order->id,
                'metadata[order_code]' => $order->order_code,
                'automatic_tax[enabled]' => 'false', // Tắt thuế tự động để giá chính xác
            ];

            if ($order->user && $order->user->email) {
                $formData['customer_email'] = $order->user->email;
            }

            // Thêm line_items với format đúng
            foreach ($lineItems as $index => $item) {
                $formData["line_items[{$index}][price_data][currency]"] = $item['price_data']['currency'];
                $formData["line_items[{$index}][price_data][product_data][name]"] = $item['price_data']['product_data']['name'];
                $formData["line_items[{$index}][price_data][unit_amount]"] = $item['price_data']['unit_amount'];
                $formData["line_items[{$index}][quantity]"] = $item['quantity'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->asForm()->post('https://api.stripe.com/v1/checkout/sessions', $formData);

            $dataRes = $response->json();

            Log::info('Stripe create checkout session response', ['data' => $dataRes]);

            if ($response->failed() || !isset($dataRes['url'])) {
                $errorMsg = $dataRes['error']['message'] ?? 'Không tạo được liên kết thanh toán Stripe.';
                return redirect()->route('payment.checkout')
                    ->with('error', $errorMsg);
            }

            return redirect()->away($dataRes['url']);
        } catch (\Throwable $e) {
            Log::error('Stripe create checkout session error', ['error' => $e->getMessage()]);
            return redirect()->route('payment.checkout')
                ->with('error', 'Stripe đang gặp sự cố. Vui lòng chọn phương thức khác.');
        }
    }

    /**
     * Callback khi người dùng được Stripe redirect về site sau khi thanh toán thành công.
     */
    public function stripeSuccess(Request $request, Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('payment.checkout')
                ->with('error', 'Thiếu thông tin phiên thanh toán.');
        }

        // Verify session với Stripe (optional, webhook sẽ xử lý chính)
        // Ở đây chỉ redirect đến success page
        $this->clearCartSession();
        return redirect()->route('payment.success', $order)
            ->with('success', 'Thanh toán thành công! Đơn hàng của bạn đang được xử lý.');
    }

    /**
     * Webhook từ Stripe (server to server).
     */
    public function stripeWebhook(Request $request)
    {
        $config = $this->stripeConfig();
        if (!$config) {
            Log::error('Stripe webhook: Chưa cấu hình Stripe');
            return response()->json(['error' => 'Chưa cấu hình'], 400);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = $config['webhook_secret'] ?? null;

        if (!$webhookSecret) {
            Log::warning('Stripe webhook: Chưa cấu hình webhook secret, bỏ qua verification');
        } else {
            // Verify webhook signature
            try {
                // Stripe signature verification logic (simplified)
                // In production, use Stripe SDK or proper signature verification
                $event = json_decode($payload, true);
            } catch (\Throwable $e) {
                Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $event = json_decode($payload, true);
        Log::info('Stripe webhook received', ['event_type' => $event['type'] ?? 'unknown']);

        if (isset($event['type']) && $event['type'] === 'checkout.session.completed') {
            $session = $event['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;
            $orderCode = $session['metadata']['order_code'] ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
            } elseif ($orderCode) {
                $order = Order::where('order_code', $orderCode)->first();
            } else {
                Log::error('Stripe webhook: Không tìm thấy order_id hoặc order_code trong metadata');
                return response()->json(['error' => 'Missing order info'], 400);
            }

            if (!$order) {
                Log::error('Stripe webhook: Đơn hàng không tồn tại', ['order_id' => $orderId, 'order_code' => $orderCode]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            if ($session['payment_status'] === 'paid') {
                $order->update(['status' => 'processing']);
                $this->clearCartSession();
                Log::info('Stripe webhook: Cập nhật đơn hàng thành công', ['order_id' => $order->id]);
            }
        }

        return response()->json(['received' => true]);
    }

    private function stripeConfig(): ?array
    {
        $cfg = Config::get('services.stripe');
        if (
            empty($cfg['key']) ||
            empty($cfg['secret'])
        ) {
            return null;
        }
        return $cfg;
    }

    /**
     * Khởi tạo thanh toán VNPAY-QR và chuyển hướng đến trang thanh toán VNPAY.
     */
    protected function startVnpayPayment(Order $order)
    {
        $config = $this->vnpayConfig();
        if (!$config) {
            return redirect()->route('payment.checkout')
                ->with('error', 'Chưa cấu hình thông tin VNPAY. Vui lòng chọn phương thức khác.');
        }

        $vnp_TmnCode = $config['tmn_code'];
        $vnp_HashSecret = $config['hash_secret'];
        $vnp_Url = $config['url'];
        $vnp_ReturnUrl = $config['return_url'] ?? route('payment.vnpay.return');

        $vnp_TxnRef = $order->order_code; // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
        $vnp_OrderType = 'other';
        $vnp_Amount = (int)$order->total_price * 100; // VNPAY yêu cầu số tiền nhân 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        // Sắp xếp dữ liệu theo thứ tự alphabet
        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . '?' . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        Log::info('VNPAY payment URL created', ['order_id' => $order->id, 'url' => $vnp_Url]);

        return redirect()->away($vnp_Url);
    }

    /**
     * Callback khi người dùng được VNPAY redirect về site sau khi thanh toán.
     */
    public function vnpayReturn(Request $request)
    {
        $config = $this->vnpayConfig();
        if (!$config) {
            return redirect()->route('payment.checkout')
                ->with('error', 'Chưa cấu hình thông tin VNPAY.');
        }

        $vnp_HashSecret = $config['hash_secret'];
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = [];

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash != $vnp_SecureHash) {
            Log::error('VNPAY return: Invalid secure hash', ['received' => $vnp_SecureHash, 'calculated' => $secureHash]);
            return redirect()->route('payment.checkout')
                ->with('error', 'Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ.');
        }

        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $vnp_Amount = $request->input('vnp_Amount');

        Log::info('VNPAY return callback', [
            'response_code' => $vnp_ResponseCode,
            'txn_ref' => $vnp_TxnRef,
            'amount' => $vnp_Amount,
        ]);

        // Tìm đơn hàng theo order_code
        $order = Order::where('order_code', $vnp_TxnRef)->first();

        if (!$order) {
            return redirect()->route('payment.checkout')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        // Kiểm tra số tiền
        if ((int)$vnp_Amount != (int)($order->total_price * 100)) {
            Log::error('VNPAY return: Amount mismatch', [
                'order_amount' => $order->total_price * 100,
                'vnpay_amount' => $vnp_Amount,
            ]);
            return redirect()->route('payment.checkout')
                ->with('error', 'Số tiền thanh toán không khớp.');
        }

        // Xử lý kết quả thanh toán
        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            $order->update(['status' => 'processing']);
            $this->clearCartSession();
            return redirect()->route('payment.success', $order)
                ->with('success', 'Thanh toán VNPAY thành công! Đơn hàng của bạn đang được xử lý.');
        } else {
            // Thanh toán thất bại
            $responseMessage = $this->getVnpayResponseMessage($vnp_ResponseCode);
            return redirect()->route('payment.checkout')
                ->with('error', 'Thanh toán VNPAY thất bại: ' . $responseMessage);
        }
    }

    /**
     * Lấy thông báo lỗi từ mã phản hồi VNPAY.
     */
    private function getVnpayResponseMessage($responseCode): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking',
            '10' => 'Xác thực thông tin thẻ/tài khoản không đúng. Quá 3 lần',
            '11' => 'Đã hết hạn chờ thanh toán. Xin vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản bị khóa.',
            '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP). Quá 3 lần.',
            '51' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Lỗi không xác định',
        ];

        return $messages[$responseCode] ?? 'Mã lỗi: ' . $responseCode;
    }

    private function vnpayConfig(): ?array
    {
        $cfg = Config::get('services.vnpay');
        if (
            empty($cfg['tmn_code']) ||
            empty($cfg['hash_secret'])
        ) {
            return null;
        }
        return $cfg;
    }

    private function clearCartSession(): void
    {
        session()->forget('cart');
        session()->forget('cart_count');
        session()->forget('cart_total');
    }
}
