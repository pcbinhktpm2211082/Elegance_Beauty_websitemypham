<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        
        $products = Product::with(['category', 'images', 'variants'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($category_id, function ($query, $category_id) {
                $query->where('category_id', $category_id);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $categories = \App\Models\Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|integer',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants.*.delete_image' => 'nullable|boolean',
        ]);

        // Tạo sản phẩm
        $productData = collect($validated)->except(['variants', 'images'])->toArray();
        $productData['is_featured'] = $request->has('is_featured');
        $product = Product::create($productData);

        // Lưu biến thể
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $variantData) {
                $variantImagePath = null;
                
                // Xử lý hình ảnh cho biến thể
                if (isset($variantData['image']) && $variantData['image']->isValid()) {
                    $variantImagePath = $variantData['image']->store('variants', 'public');
                }

                $variant = $product->variants()->create([
                    'variant_name' => $variantData['variant_name'] ?? null,
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'] ?? null,
                    'quantity' => $variantData['quantity'] ?? 0,
                    'image' => $variantImagePath,
                ]);


            }
        }

        // Lưu nhiều ảnh
        if ($request->hasFile('images')) {
            $hasCover = $product->images()->where('is_cover', true)->exists();
            foreach ($request->file('images') as $index => $image) {
                if ($image->isValid()) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_cover' => !$hasCover && $index === 0,
                        'sort_order' => $product->images()->count() + $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $product = Product::with([
            'images', 
            'variants'
        ])->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('images', 'variants.attributeValues')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_image_id' => 'nullable|integer|exists:product_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:product_images,id',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|integer',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants.*.delete_image' => 'nullable|boolean',
        ]);

        // Cập nhật sản phẩm
        $productData = collect($validated)->except(['variants', 'images', 'delete_images', 'cover_image_id'])->toArray();
        $productData['is_featured'] = $request->has('is_featured');
        $product->update($productData);

        // Thay thế ảnh mới (nếu có) - xử lý trước để tránh xung đột
        if ($request->hasFile('images')) {
            // Xóa tất cả ảnh cũ
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }
            
            // Thêm ảnh mới
            foreach ($request->file('images') as $index => $image) {
                if ($image->isValid()) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_cover' => $index === 0, // Ảnh đầu tiên làm ảnh bìa
                        'sort_order' => $index,
                    ]);
                }
            }
        } else {
            // Chỉ xử lý khi không thay thế ảnh mới
            
            // Xóa ảnh cụ thể
            if (!empty($validated['delete_images'])) {
                foreach ($validated['delete_images'] as $imageId) {
                    $img = $product->images()->find($imageId);
                    if ($img) {
                        if (Storage::disk('public')->exists($img->image_path)) {
                            Storage::disk('public')->delete($img->image_path);
                        }
                        $img->delete();
                    }
                }
            }

            // Cập nhật ảnh bìa (chỉ khi không xóa tất cả ảnh)
            if (!empty($validated['cover_image_id'])) {
                $product->images()->update(['is_cover' => false]);
                $coverImage = $product->images()->find($validated['cover_image_id']);
                if ($coverImage) {
                    $coverImage->update(['is_cover' => true]);
                }
            }
        }

        // Cập nhật biến thể
        $variants = $validated['variants'] ?? [];
        $existingVariantIds = $product->variants()->pluck('id')->toArray();
        $inputVariantIds = collect($variants)->pluck('id')->filter()->toArray();

        // Xóa biến thể không còn
        $deleteIds = array_diff($existingVariantIds, $inputVariantIds);
        if (!empty($deleteIds)) {
            // Xóa ảnh của các biến thể bị xóa
            $variantsToDelete = ProductVariant::whereIn('id', $deleteIds)->get();
            foreach ($variantsToDelete as $variant) {
                if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }
            }
            ProductVariant::destroy($deleteIds);
        }

        // Cập nhật hoặc thêm mới
        if (!empty($variants)) {
            foreach ($variants as $variantData) {
                if (!empty($variantData['id'])) {
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variantImagePath = $variant->image;
                        
                        // Xử lý xóa ảnh biến thể
                        if (!empty($variantData['delete_image'])) {
                            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                                Storage::disk('public')->delete($variant->image);
                            }
                            $variantImagePath = null;
                        }
                        // Xử lý hình ảnh mới cho biến thể
                        elseif (isset($variantData['image']) && $variantData['image']->isValid()) {
                            // Xóa ảnh cũ nếu có
                            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                                Storage::disk('public')->delete($variant->image);
                            }
                            $variantImagePath = $variantData['image']->store('variants', 'public');
                        }

                        $variant->update([
                            'variant_name' => $variantData['variant_name'] ?? null,
                            'sku' => $variantData['sku'] ?? null,
                            'price' => $variantData['price'] ?? null,
                            'quantity' => $variantData['quantity'] ?? 0,
                            'image' => $variantImagePath,
                        ]);
                        

                    }
                } else {
                    $variantImagePath = null;
                    
                    // Xử lý hình ảnh cho biến thể mới
                    if (isset($variantData['image']) && $variantData['image']->isValid()) {
                        $variantImagePath = $variantData['image']->store('variants', 'public');
                    }

                    $newVariant = $product->variants()->create([
                        'variant_name' => $variantData['variant_name'] ?? null,
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? null,
                        'quantity' => $variantData['quantity'] ?? 0,
                        'image' => $variantImagePath,
                    ]);
                    

                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        // Xóa hình ảnh sản phẩm
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
        
        // Xóa hình ảnh biến thể
        foreach ($product->variants as $variant) {
            if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                Storage::disk('public')->delete($variant->image);
            }
        }
        
        $product->variants()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xoá sản phẩm!');
    }

    public function show($id)
    {
        $product = Product::with([
            'images', 
            'category', 
            'variants'
        ])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    

}
