<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\ProductClassification;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        $skinTypes = ProductClassification::where('type', 'skin_type')->get();
        $skinConcerns = ProductClassification::where('type', 'skin_concern')->get();
        return view('admin.products.create', compact('categories', 'skinTypes', 'skinConcerns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'product_type' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'sometimes|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'variants' => 'nullable|array',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|integer',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.image' => 'sometimes|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'variants.*.delete_image' => 'nullable|boolean',
        ]);

        // Tạo sản phẩm
        $productData = collect($validated)->except(['variants', 'images'])->toArray();
        $productData['is_featured'] = $request->has('is_featured');
        $productData['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
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
                    'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
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

        // Lưu phân loại sản phẩm
        if ($request->has('classifications')) {
            $product->classifications()->sync($request->classifications);
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $product = Product::with([
            'images', 
            'variants',
            'classifications'
        ])->findOrFail($id);
        $categories = Category::all();
        $skinTypes = ProductClassification::where('type', 'skin_type')->get();
        $skinConcerns = ProductClassification::where('type', 'skin_concern')->get();
        return view('admin.products.edit', compact('product', 'categories', 'skinTypes', 'skinConcerns'));
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
            'product_type' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'sometimes|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'cover_image_id' => 'nullable|integer|exists:product_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:product_images,id',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.variant_name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.quantity' => 'nullable|integer',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.image' => 'sometimes|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'variants.*.delete_image' => 'nullable|boolean',
            'classifications' => 'nullable|array',
            'classifications.*' => 'exists:product_classifications,id',
        ]);

        // Cập nhật sản phẩm
        $productData = collect($validated)->except(['variants', 'images', 'delete_images', 'cover_image_id'])->toArray();
        $productData['is_featured'] = $request->has('is_featured');
        $productData['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;
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
                            'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
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
                        'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
                    ]);
                    

                }
            }
        }

        // Cập nhật phân loại sản phẩm
        if ($request->has('classifications')) {
            $product->classifications()->sync($request->classifications);
        } else {
            $product->classifications()->detach();
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        // Kiểm tra xem sản phẩm có đơn hàng liên quan không
        $orderItemsCount = OrderItem::where('product_id', $product->id)->count();
        
        if ($orderItemsCount > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', "Không thể xóa sản phẩm này vì có {$orderItemsCount} đơn hàng đang sử dụng. Vui lòng vô hiệu hóa sản phẩm (is_active = false) thay vì xóa.");
        }

        // Kiểm tra xem sản phẩm có trong giỏ hàng không
        try {
            $cartItemsCount = DB::table('carts')->where('product_id', $product->id)->count();
            if ($cartItemsCount > 0) {
                // Xóa các item trong giỏ hàng liên quan
                DB::table('carts')->where('product_id', $product->id)->delete();
            }
        } catch (\Exception $e) {
            // Bảng carts có thể không tồn tại, bỏ qua
        }

        // Xóa lịch sử xem sản phẩm
        try {
            \App\Models\ProductView::where('product_id', $product->id)->delete();
        } catch (\Exception $e) {
            // Bỏ qua nếu có lỗi
        }

        // Xóa phân loại sản phẩm
        $product->classifications()->detach();

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
