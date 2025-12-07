<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductClassification;
use App\Models\ProductType;
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

        $productTypes = ProductType::orderBy('name')->get();

        return view('admin.product-classifications.index', compact('classifications', 'productTypes'));
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

    /**
     * Store product type
     */
    public function storeProductType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name',
            'requires_skin_type_filter' => 'nullable|boolean',
        ]);

        ProductType::create([
            'name' => $request->name,
            'requires_skin_type_filter' => $request->has('requires_skin_type_filter') ? (bool)$request->requires_skin_type_filter : true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm loại sản phẩm thành công!',
                'productType' => ProductType::latest()->first()
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Đã thêm loại sản phẩm thành công!');
    }

    /**
     * Update product type
     */
    public function updateProductType(Request $request, ProductType $productType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name,' . $productType->id,
            'requires_skin_type_filter' => 'nullable|boolean',
        ]);

        $productType->update([
            'name' => $request->name,
            'requires_skin_type_filter' => $request->has('requires_skin_type_filter') ? (bool)$request->requires_skin_type_filter : true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật loại sản phẩm thành công!',
                'productType' => $productType->fresh()
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Cập nhật loại sản phẩm thành công!');
    }

    /**
     * Destroy product type
     */
    public function destroyProductType(ProductType $productType)
    {
        $productsCount = \App\Models\Product::where('product_type', $productType->name)->count();
        
        if ($productsCount > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xóa loại sản phẩm này vì có {$productsCount} sản phẩm đang sử dụng."
                ], 422);
            }
            return redirect()->route('admin.product-classifications.index')
                ->with('error', "Không thể xóa loại sản phẩm này vì có {$productsCount} sản phẩm đang sử dụng.");
        }

        $productType->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa loại sản phẩm thành công!'
            ]);
        }

        return redirect()->route('admin.product-classifications.index')
            ->with('success', 'Xóa loại sản phẩm thành công!');
    }
}
