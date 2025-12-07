<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Helper method để load đầy đủ dữ liệu cho products
     */
    private function loadProductData($query)
    {
        return $query->with(['images', 'category', 'classifications', 'variants' => function($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['reviews as approved_reviews_count' => function($q) {
                $q->where('is_approved', true);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', true);
            }], 'rating')
            ->selectRaw('products.*, (SELECT COALESCE(SUM(order_items.quantity), 0) FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE order_items.product_id = products.id AND orders.status IN ("processing", "shipped", "delivered")) as sales_count');
    }

    /**
     * Content-Based Filtering: Gợi ý dựa trên loại da và vấn đề da
     * Sử dụng RecommendationService với logic 4 bước
     */
    public function contentBased(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 12);

        if (!$user) {
            // Nếu chưa đăng nhập, trả về sản phẩm nổi bật
            $products = $this->loadProductData(
                Product::where('is_active', true)->where('is_featured', true)
            )->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'type' => 'featured',
                'message' => 'Gợi ý sản phẩm nổi bật (chưa đăng nhập)'
            ]);
        }

        if (!$user->skin_type) {
            // Nếu chưa có thông tin da, trả về sản phẩm nổi bật
            $products = $this->loadProductData(
                Product::where('is_active', true)->where('is_featured', true)
            )->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'type' => 'featured',
                'message' => 'Gợi ý sản phẩm nổi bật (chưa có thông tin loại da)'
            ]);
        }

        // Sử dụng RecommendationService để lấy gợi ý
        $products = $this->recommendationService->getRecommendations($user, $limit);

        return response()->json([
            'success' => true,
            'products' => $products->values(),
            'type' => 'content-based',
            'user_skin_type' => $user->skin_type_text,
            'user_concerns' => $user->skin_concerns ?? [],
        ]);
    }

    /**
     * View History: Gợi ý dựa trên lịch sử xem sản phẩm
     */
    public function viewHistory(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 12);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để xem gợi ý'
            ], 401);
        }

        // Lấy các sản phẩm đã xem gần đây (30 ngày)
        $recentViews = ProductView::where('user_id', $user->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->with('product.classifications')
            ->orderBy('viewed_at', 'desc')
            ->limit(20)
            ->get();

        if ($recentViews->isEmpty()) {
            // Nếu chưa có lịch sử, trả về sản phẩm nổi bật
            $products = $this->loadProductData(
                Product::where('is_active', true)->where('is_featured', true)
            )->limit($limit)->get();
            
            return response()->json([
                'success' => true,
                'products' => $products,
                'type' => 'featured',
                'message' => 'Gợi ý sản phẩm nổi bật (chưa có lịch sử xem)'
            ]);
        }

        // Lấy các loại da và vấn đề da từ sản phẩm đã xem
        $viewedProductIds = $recentViews->pluck('product_id')->toArray();
        $viewedProducts = Product::whereIn('id', $viewedProductIds)
            ->where('is_active', true)
            ->with(['classifications', 'variants' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();

        $skinTypeIds = [];
        $skinConcernIds = [];
        
        foreach ($viewedProducts as $product) {
            foreach ($product->classifications as $classification) {
                if ($classification->type === 'skin_type') {
                    $skinTypeIds[] = $classification->id;
                } else {
                    $skinConcernIds[] = $classification->id;
                }
            }
        }

        // Tìm sản phẩm tương tự dựa trên phân loại
        $query = $this->loadProductData(
            Product::where('is_active', true)->whereNotIn('id', $viewedProductIds)
        );

        // Tìm sản phẩm có cùng loại da hoặc vấn đề da
        if (!empty($skinTypeIds) || !empty($skinConcernIds)) {
            $query->whereHas('classifications', function($q) use ($skinTypeIds, $skinConcernIds) {
                if (!empty($skinTypeIds)) {
                    $q->whereIn('product_classifications.id', $skinTypeIds);
                }
                if (!empty($skinConcernIds)) {
                    $q->orWhereIn('product_classifications.id', $skinConcernIds);
                }
            });
        }

        $products = $query->limit($limit)->get();

        // Nếu không đủ, bổ sung sản phẩm cùng danh mục
        if ($products->count() < $limit) {
            $categoryIds = $viewedProducts->pluck('category_id')->unique()->toArray();
            $existingIds = array_merge($viewedProductIds, $products->pluck('id')->toArray());
            
            $additionalProducts = $this->loadProductData(
                Product::where('is_active', true)
                ->whereIn('category_id', $categoryIds)
                ->whereNotIn('id', $existingIds)
            )->limit($limit - $products->count())->get();
            
            $products = $products->merge($additionalProducts);
        }

        // Nếu vẫn không đủ, bổ sung sản phẩm nổi bật
        if ($products->count() < $limit) {
            $existingIds = array_merge($viewedProductIds, $products->pluck('id')->toArray());
            $featuredProducts = $this->loadProductData(
                Product::where('is_active', true)
                ->where('is_featured', true)
                ->whereNotIn('id', $existingIds)
            )->limit($limit - $products->count())->get();
            
            $products = $products->merge($featuredProducts);
        }

        return response()->json([
            'success' => true,
            'products' => $products->take($limit),
            'type' => 'view-history',
            'viewed_count' => $recentViews->count(),
        ]);
    }

    /**
     * Gợi ý tổng hợp (kết hợp cả 2 hệ thống)
     */
    public function hybrid(Request $request)
    {
        $contentBased = $this->contentBased($request);
        $viewHistory = $this->viewHistory($request);

        $contentBasedData = json_decode($contentBased->getContent(), true);
        $viewHistoryData = json_decode($viewHistory->getContent(), true);

        // Kết hợp và loại bỏ trùng lặp
        $allProducts = collect($contentBasedData['products'] ?? [])
            ->merge($viewHistoryData['products'] ?? [])
            ->unique('id')
            ->take($request->get('limit', 12));

        return response()->json([
            'success' => true,
            'products' => $allProducts->values(),
            'type' => 'hybrid',
            'content_based_count' => count($contentBasedData['products'] ?? []),
            'view_history_count' => count($viewHistoryData['products'] ?? []),
        ]);
    }

    /**
     * Routine/Bundle Recommendations: Gợi ý sản phẩm bổ sung cho routine chăm sóc da
     * Dựa trên sản phẩm hiện tại đang xem
     */
    public function routineRecommendations(Request $request, $productId)
    {
        $product = Product::with('classifications')->findOrFail($productId);
        $limit = $request->get('limit', 6);

        // Lấy các phân loại của sản phẩm hiện tại
        $productSkinTypes = $product->classifications->where('type', 'skin_type')->pluck('id')->toArray();
        $productSkinConcerns = $product->classifications->where('type', 'skin_concern')->pluck('id')->toArray();
        $productCategoryId = $product->category_id;

        // Tìm sản phẩm có cùng mục đích (cùng skin_type và skin_concerns)
        $query = $this->loadProductData(
            Product::where('is_active', true)->where('id', '!=', $productId)
        );

        // Ưu tiên sản phẩm có cùng loại da và vấn đề da
        if (!empty($productSkinTypes) || !empty($productSkinConcerns)) {
            $query->whereHas('classifications', function($q) use ($productSkinTypes, $productSkinConcerns) {
                if (!empty($productSkinTypes)) {
                    $q->whereIn('product_classifications.id', $productSkinTypes);
                }
                if (!empty($productSkinConcerns)) {
                    $q->orWhereIn('product_classifications.id', $productSkinConcerns);
                }
            });
        }

        $products = $query->limit($limit)->get();

        // Nếu không đủ, bổ sung sản phẩm cùng danh mục nhưng khác loại
        if ($products->count() < $limit) {
            $existingIds = array_merge([$productId], $products->pluck('id')->toArray());
            
            $additionalProducts = $this->loadProductData(
                Product::where('is_active', true)
                ->where('category_id', $productCategoryId)
                ->whereNotIn('id', $existingIds)
            )->limit($limit - $products->count())->get();
            
            $products = $products->merge($additionalProducts);
        }

        // Nếu vẫn không đủ, bổ sung sản phẩm nổi bật
        if ($products->count() < $limit) {
            $existingIds = array_merge([$productId], $products->pluck('id')->toArray());
            
            $featuredProducts = $this->loadProductData(
                Product::where('is_active', true)
                ->where('is_featured', true)
                ->whereNotIn('id', $existingIds)
            )->limit($limit - $products->count())->get();
            
            $products = $products->merge($featuredProducts);
        }

        return response()->json([
            'success' => true,
            'products' => $products->take($limit),
            'type' => 'routine',
            'current_product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
        ]);
    }
}
