<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductClassification;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with(['images', 'variants' => function($q) {
                $q->where('is_active', true);
            }, 'category', 'classifications'])
            ->withCount(['reviews as approved_reviews_count' => function($q) {
                $q->where('is_approved', true);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', true);
            }], 'rating')
            ->selectRaw('products.*, (SELECT COALESCE(SUM(order_items.quantity), 0) FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE order_items.product_id = products.id AND orders.status IN ("processing", "shipped", "delivered")) as sales_count');
        
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

        // Lọc theo danh mục (dropdown - chỉ chọn 1)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        } elseif ($request->filled('categories')) {
            // Fallback cho tham số cũ (nhiều danh mục)
            $categories = is_array($request->categories) ? $request->categories : [$request->categories];
            $categories = array_filter($categories, function($value) {
                return !empty($value) && $value !== null;
            });
            
            if (!empty($categories)) {
                $query->whereIn('category_id', $categories);
            }
        }

        // Lọc theo giá
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }


        // Lọc theo loại da
        if ($request->filled('skin_type')) {
            $skinTypeId = $request->skin_type;
            $query->whereHas('classifications', function($q) use ($skinTypeId) {
                $q->where('product_classifications.id', $skinTypeId)
                  ->where('product_classifications.type', 'skin_type');
            });
        }

        // Lọc theo tình trạng da (skin concern)
        if ($request->filled('skin_concern')) {
            $skinConcernId = $request->skin_concern;
            $query->whereHas('classifications', function($q) use ($skinConcernId) {
                $q->where('product_classifications.id', $skinConcernId)
                  ->where('product_classifications.type', 'skin_concern');
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
            case 'rating':
                // Sắp xếp theo đánh giá trung bình (giảm dần hoặc tăng dần)
                // Sử dụng COALESCE để xử lý sản phẩm chưa có đánh giá (sẽ có giá trị 0)
                $query->orderByRaw('COALESCE((SELECT AVG(rating) FROM product_reviews WHERE product_reviews.product_id = products.id AND product_reviews.is_approved = 1), 0) ' . $sortOrder);
                break;
            case 'sales':
                // Sắp xếp theo số lượt bán (tổng quantity từ order_items của các order đã xác nhận)
                $query->orderByRaw('(SELECT COALESCE(SUM(order_items.quantity), 0) FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE order_items.product_id = products.id AND orders.status IN ("processing", "shipped", "delivered")) ' . $sortOrder);
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
        
        // Lọc biến thể không hoạt động cho từng sản phẩm
        $products->getCollection()->transform(function($product) {
            $product->variants = $product->variants->filter(function($variant) {
                return $variant->is_active ?? true;
            });
            return $product;
        });
        
        $categories = Category::all();
        $skinTypes = ProductClassification::where('type', 'skin_type')->orderBy('name')->get();
        $skinConcerns = ProductClassification::where('type', 'skin_concern')->orderBy('name')->get();
        
        return view('user.products.index', compact('products', 'categories', 'skinTypes', 'skinConcerns'));
    }

    public function show(Product $product)
    {
        // Kiểm tra sản phẩm có đang hoạt động không
        if (!$product->is_active) {
            abort(404);
        }
        
        $product->load(['images', 'variants' => function($q) {
            $q->where('is_active', true);
        }, 'category', 'classifications'])
        ->loadCount(['reviews as approved_reviews_count' => function($q) {
            $q->where('is_approved', true);
        }])
        ->loadAvg(['reviews as avg_rating' => function($q) {
            $q->where('is_approved', true);
        }], 'rating');
        
        // Track view history
        $this->trackView($product);
        
        return view('user.products.show', compact('product'));
    }

    /**
     * Track product view for recommendation system
     */
    private function trackView(Product $product)
    {
        $user = auth()->user();
        $ipAddress = request()->ip();

        \App\Models\ProductView::create([
            'user_id' => $user ? $user->id : null,
            'product_id' => $product->id,
            'ip_address' => $ipAddress,
            'viewed_at' => now(),
        ]);
    }
}
