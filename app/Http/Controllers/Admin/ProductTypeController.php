<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productTypes = ProductType::orderBy('name')->get();
        return view('admin.product-types.index', compact('productTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name',
            'requires_skin_type_filter' => 'nullable|boolean',
        ]);

        ProductType::create([
            'name' => $request->name,
            'requires_skin_type_filter' => $request->has('requires_skin_type_filter') ? (bool)$request->requires_skin_type_filter : true,
        ]);

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Đã thêm loại sản phẩm thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductType $productType)
    {
        return view('admin.product-types.edit', compact('productType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_types,name,' . $productType->id,
            'requires_skin_type_filter' => 'nullable|boolean',
        ]);

        $productType->update([
            'name' => $request->name,
            'requires_skin_type_filter' => $request->has('requires_skin_type_filter') ? (bool)$request->requires_skin_type_filter : true,
        ]);

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Cập nhật loại sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        // Kiểm tra xem có sản phẩm nào đang sử dụng loại này không
        $productsCount = \App\Models\Product::where('product_type', $productType->name)->count();
        
        if ($productsCount > 0) {
            return redirect()->route('admin.product-types.index')
                ->with('error', "Không thể xóa loại sản phẩm này vì có {$productsCount} sản phẩm đang sử dụng.");
        }

        $productType->delete();

        return redirect()->route('admin.product-types.index')
            ->with('success', 'Xóa loại sản phẩm thành công!');
    }
}
