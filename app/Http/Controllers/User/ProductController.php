<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images', 'variants', 'category']);
        
        // Debug: Log filter parameters
        if ($request->filled('categories') || $request->filled('category')) {
            \Log::info('Filter Debug:', [
                'categories_param' => $request->get('categories'),
                'category_param' => $request->get('category'),
                'request_all' => $request->all()
            ]);
        }

        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục (nhiều danh mục)
        if ($request->filled('categories')) {
            $categories = is_array($request->categories) ? $request->categories : [$request->categories];
            // Filter out any empty values
            $categories = array_filter($categories, function($value) {
                return !empty($value) && $value !== null;
            });
            
            if (!empty($categories)) {
                $query->whereIn('category_id', $categories);
            }
        } elseif ($request->filled('category')) { // fallback tham số cũ
            $query->where('category_id', $request->category);
        }

        // Lọc theo giá
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Lọc khuyến mãi (dựa trên is_featured)
        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        // Lọc còn hàng
        if ($request->boolean('in_stock')) {
            $query->where(function($q) {
                $q->where('quantity', '>', 0)
                  ->orWhereHas('variants', function($v) {
                      $v->where('quantity', '>', 0);
                  });
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        // Nếu có limit parameter (cho header search)
        if ($request->filled('limit')) {
            $products = $query->limit($request->limit)->get();
            
            // Format products cho header search
            $formattedProducts = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->coverOrFirstImage ? asset('storage/' . $product->coverOrFirstImage) : null,
                    'category' => $product->category ? $product->category->name : null
                ];
            });
            
            return response()->json(['products' => $formattedProducts]);
        }

        $products = $query->paginate(12)->appends($request->query());
        $categories = Category::all();
        
        return view('user.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['images', 'variants', 'category']);
        
        return view('user.products.show', compact('product'));
    }
}
