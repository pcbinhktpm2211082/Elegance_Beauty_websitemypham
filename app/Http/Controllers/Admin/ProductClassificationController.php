<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductClassification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductClassificationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        
        $classifications = ProductClassification::query()
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('admin.product-classifications.index', compact('classifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:skin_type,skin_concern',
        ]);

        ProductClassification::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm nhãn phân loại thành công!',
                'classification' => ProductClassification::latest()->first()
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Đã thêm nhãn phân loại thành công!');
    }

    public function update(Request $request, ProductClassification $productClassification)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:skin_type,skin_concern',
        ]);

        $productClassification->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhãn phân loại thành công!',
                'classification' => $productClassification->fresh()
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Cập nhật nhãn phân loại thành công!');
    }

    public function destroy(ProductClassification $productClassification)
    {
        // Kiểm tra xem có sản phẩm nào đang sử dụng phân loại này không
        $productsCount = $productClassification->products()->count();
        
        if ($productsCount > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa nhãn này vì có {$productsCount} sản phẩm đang sử dụng."
                ], 422);
            }
            return redirect()->route('admin.product-classifications.index')
                ->with('error', "Không thể xóa nhãn này vì có {$productsCount} sản phẩm đang sử dụng.");
        }

        $productClassification->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa nhãn phân loại thành công!'
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Xóa nhãn phân loại thành công!');
    }
}
