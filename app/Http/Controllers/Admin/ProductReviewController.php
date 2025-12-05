<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviewsQuery = ProductReview::with(['product', 'user'])
            ->orderByDesc('created_at');

        if ($request->filled('product')) {
            $reviewsQuery->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->product.'%');
            });
        }

        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->rating);
        }

        $reviews = $reviewsQuery->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'admin_reply' => 'nullable|string|max:2000',
        ], [
            'admin_reply.max' => 'Phản hồi không được vượt quá 2000 ký tự.',
        ]);

        $review->admin_reply = $data['admin_reply'] ?? null;
        $review->admin_replied_at = $data['admin_reply'] ? now() : null;
        $review->save();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Đã cập nhật phản hồi cho đánh giá.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đã xoá đánh giá.');
    }
}


