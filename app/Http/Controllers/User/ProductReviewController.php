<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductReviewController extends Controller
{
    /**
     * Lưu đánh giá sản phẩm từ người dùng.
     */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đánh giá sản phẩm.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:4096',
            'order_id' => 'nullable|integer|exists:orders,id',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min' => 'Số sao tối thiểu là 1.',
            'rating.max' => 'Số sao tối đa là 5.',
            'images.max' => 'Bạn chỉ có thể tải lên tối đa 5 ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.max' => 'Mỗi ảnh không được vượt quá 4MB.',
        ]);

        // Nếu có order_id trong request, sử dụng nó; nếu không, tìm đơn hàng delivered chứa sản phẩm này
        $orderId = null;
        if ($request->filled('order_id')) {
            $order = Order::find($request->order_id);
            if ($order && $order->user_id === $user->id && $order->status === Order::STATUS_DELIVERED) {
                // Kiểm tra xem đơn hàng có chứa sản phẩm này không
                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('product_id', $product->id)
                    ->first();
                if ($orderItem) {
                    $orderId = $order->id;
                }
            }
        }
        
        // Nếu không có order_id từ request, tìm đơn hàng delivered chứa sản phẩm này
        if (!$orderId) {
            $deliveredOrderItem = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('status', Order::STATUS_DELIVERED);
                })
                ->latest('id')
                ->first();

            if (!$deliveredOrderItem) {
                $errorMessage = 'Chỉ những đơn hàng đã giao thành công mới có thể đánh giá sản phẩm.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                return back()->with('error', $errorMessage);
            }
            $orderId = $deliveredOrderItem->order_id;
        }

        // Không cho trùng đánh giá cho cùng 1 đơn hàng + sản phẩm
        $existing = ProductReview::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->where('order_id', $orderId)
            ->first();

        if ($existing) {
            $errorMessage = 'Bạn đã đánh giá sản phẩm này cho đơn hàng này rồi.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            return back()->with('error', $errorMessage);
        }

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $orderId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_reviews', 'public');
                $review->images()->create(['image_path' => $path]);
            }
        }

        // Nếu là AJAX request, trả về JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                'review_id' => $review->id,
                'order_id' => $orderId,
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}


