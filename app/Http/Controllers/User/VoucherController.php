<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::orderByDesc('created_at')->paginate(12);
        return view('user.vouchers.index', compact('vouchers'));
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Mã voucher không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = trim((string)$request->input('voucher_code'));
        $cartItems = session('cart', []);

        if (empty($cartItems)) {
            return response()->json([
                'ok' => false,
                'message' => 'Giỏ hàng trống',
            ], 400);
        }

        $subtotal = 0;
        $shippingFee = 30000;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $totalAmount = $subtotal + $shippingFee;

        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher) {
            return response()->json([
                'ok' => false,
                'message' => 'Không tìm thấy mã voucher',
            ], 404);
        }

        if (!$voucher->is_available) {
            return response()->json([
                'ok' => false,
                'message' => 'Voucher không hiệu lực',
            ], 400);
        }

        if (!is_null($voucher->min_order_amount) && $subtotal < $voucher->min_order_amount) {
            return response()->json([
                'ok' => false,
                'message' => 'Chưa đạt giá trị đơn tối thiểu',
            ], 400);
        }

        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') {
            $discountAmount = round($subtotal * ($voucher->discount_value / 100));
        } else {
            $discountAmount = min($totalAmount, $voucher->discount_value);
        }
        $newTotal = max(0, $totalAmount - $discountAmount);

        return response()->json([
            'ok' => true,
            'message' => 'Áp dụng voucher thành công',
            'data' => [
                'subtotal' => $subtotal,
                'shipping' => $shippingFee,
                'discount' => (int)$discountAmount,
                'total' => (int)$newTotal,
            ],
        ]);
    }
}



