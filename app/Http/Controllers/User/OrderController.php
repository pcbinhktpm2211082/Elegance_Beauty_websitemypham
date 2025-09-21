<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $orders = $user->orders()->orderBy('created_at', 'desc')->paginate(10);
        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Kiểm tra xem order có thuộc về user hiện tại không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('user.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Kiểm tra xem order có thuộc về user hiện tại không
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền hủy đơn hàng này.'
            ], 403);
        }

        // Kiểm tra xem đơn hàng có thể hủy không (chỉ đơn hàng pending mới có thể hủy)
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn hàng đang chờ xử lý.'
            ], 400);
        }

        try {
            $order->update(['status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đơn hàng.'
            ], 500);
        }
    }
}
