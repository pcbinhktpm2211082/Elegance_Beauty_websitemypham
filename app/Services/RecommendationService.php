<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductView;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Bước 1: Sàng lọc Loại Sản phẩm (Product Type Bypass)
     * Xác định các sản phẩm được bỏ qua bộ lọc Loại da
     * Kiểm tra từ bảng product_types: nếu requires_skin_type_filter = FALSE thì bỏ qua filter
     */
    private function shouldBypassSkinTypeFilter(?string $productTypeName): bool
    {
        if (!$productTypeName) {
            return false; // Nếu không có product_type, áp dụng filter
        }

        $productType = ProductType::where('name', $productTypeName)->first();
        
        // Nếu không tìm thấy trong bảng product_types, mặc định áp dụng filter (an toàn)
        if (!$productType) {
            return false;
        }

        // Nếu requires_skin_type_filter = FALSE, bỏ qua filter
        return !$productType->requires_skin_type_filter;
    }

    /**
     * Bước 2: Bộ lọc An toàn Cấp 1 (Hard Filter - Da Mặt)
     * Loại bỏ sản phẩm chăm sóc da mặt không phù hợp với loại da của user
     */
    private function applyHardFilter(Collection $products, User $user): Collection
    {
        $userSkinTypeName = $this->getUserSkinTypeName($user);

        if (!$userSkinTypeName) {
            return $products;
        }

        return $products->filter(function ($product) use ($userSkinTypeName) {
            // Bỏ qua filter cho các loại sản phẩm có requires_skin_type_filter = FALSE
            if ($this->shouldBypassSkinTypeFilter($product->product_type)) {
                return true;
            }

            // Đối với sản phẩm chăm sóc da mặt, kiểm tra target_skin_type
            // Sử dụng relationship đã được eager load
            $productSkinTypes = $product->classifications
                ->where('type', 'skin_type')
                ->pluck('name')
                ->toArray();

            // Nếu sản phẩm có "Phù hợp với mọi tình trạng da", luôn cho qua
            if (in_array('Phù hợp với mọi tình trạng da', $productSkinTypes)) {
                return true;
            }

            // Kiểm tra xem sản phẩm có phù hợp với loại da của user không
            return in_array($userSkinTypeName, $productSkinTypes);
        });
    }

    /**
     * Bước 3: Bộ lọc An toàn Cấp 2 (Sensitivity Filter)
     * Loại bỏ sản phẩm không có tag an toàn nếu user nhạy cảm
     */
    private function applySensitivityFilter(Collection $products, User $user): Collection
    {
        // Kiểm tra xem user có concern 'Sensitive' không
        $userConcerns = $user->skin_concerns ?? [];
        $isSensitive = $user->is_sensitive || in_array('sensitive', $userConcerns);

        if (!$isSensitive) {
            return $products;
        }

        // Nếu user nhạy cảm, chỉ giữ lại sản phẩm có sensitive_flags
        return $products->filter(function ($product) {
            $sensitiveFlags = $product->sensitive_flags ?? [];
            return !empty($sensitiveFlags) && (
                in_array('Alcohol-Free', $sensitiveFlags) ||
                in_array('Fragrance-Free', $sensitiveFlags) ||
                in_array('Sensitive-Safe', $sensitiveFlags)
            );
        });
    }

    /**
     * Bước 4: Tính điểm và Xếp hạng (Scoring & Ranking)
     * Tính điểm dựa trên số tag target_concern khớp với user.target_concern
     */
    private function scoreAndRank(Collection $products, User $user): Collection
    {
        $userConcerns = $this->getUserConcernNames($user);

        if (empty($userConcerns)) {
            return $products;
        }

        // Tính điểm cho mỗi sản phẩm
        $scoredProducts = $products->map(function ($product) use ($userConcerns) {
            // Sử dụng relationship đã được eager load
            $productConcerns = $product->classifications
                ->where('type', 'skin_concern')
                ->pluck('name')
                ->toArray();

            // Đếm số tag khớp
            $score = count(array_intersect($userConcerns, $productConcerns));

            return [
                'product' => $product,
                'score' => $score,
            ];
        });

        // Sắp xếp theo điểm giảm dần
        $sorted = $scoredProducts->sortByDesc('score');

        // Trả về collection sản phẩm đã sắp xếp
        return $sorted->pluck('product');
    }

    /**
     * Lấy tên loại da của user (dạng tiếng Việt)
     */
    private function getUserSkinTypeName(User $user): ?string
    {
        if (!$user->skin_type) {
            return null;
        }

        $skinTypeMap = [
            'normal' => 'Da Thường',
            'dry' => 'Da Khô',
            'oily' => 'Da Dầu/Nhờn',
            'combination' => 'Da Hỗn Hợp',
            'sensitive' => 'Da Nhạy Cảm',
        ];

        return $skinTypeMap[$user->skin_type] ?? null;
    }

    /**
     * Lấy danh sách tên concern của user (dạng tiếng Việt)
     */
    private function getUserConcernNames(User $user): array
    {
        $userConcerns = $user->skin_concerns ?? [];
        
        $concernMap = [
            'acne' => 'Mụn',
            'anti-aging' => 'Lão hóa',
            'brightening' => 'Tăng sắc tố',
            'hydration' => 'Mất nước/Thiếu ẩm',
            'sensitive' => 'Đỏ da/Kích ứng',
        ];

        $concernNames = [];
        foreach ($userConcerns as $concern) {
            if (isset($concernMap[$concern])) {
                $concernNames[] = $concernMap[$concern];
            }
        }

        return $concernNames;
    }

    /**
     * Hàm chính: Thực hiện Content-Based Filtering với 4 bước
     */
    public function getContentBasedRecommendations(User $user, int $limit = 12): Collection
    {
        // Lấy tất cả sản phẩm đang hoạt động
        $products = Product::where('is_active', true)
            ->with([
                'images',
                'category',
                'classifications',
                'variants' => function($q) {
                    $q->where('is_active', true);
                }
            ])
            ->withCount(['reviews as approved_reviews_count' => function($q) {
                $q->where('is_approved', true);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', true);
            }], 'rating')
            ->selectRaw('products.*, (SELECT COALESCE(SUM(order_items.quantity), 0) FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE order_items.product_id = products.id AND orders.status IN ("processing", "shipped", "delivered")) as sales_count')
            ->get();

        // Bước 1: Đã lấy tất cả sản phẩm (không cần xử lý gì ở đây)

        // Bước 2: Bộ lọc An toàn Cấp 1 (Hard Filter - Da Mặt)
        $filteredProducts = $this->applyHardFilter($products, $user);

        // Bước 3: Bộ lọc An toàn Cấp 2 (Sensitivity Filter)
        $filteredProducts = $this->applySensitivityFilter($filteredProducts, $user);

        // Bước 4: Tính điểm và Xếp hạng
        $rankedProducts = $this->scoreAndRank($filteredProducts, $user);

        // Giới hạn số lượng
        return $rankedProducts->take($limit);
    }

    /**
     * Bổ sung sản phẩm từ View History nếu Content-Based trả về < 5 sản phẩm
     */
    public function supplementWithViewHistory(Collection $contentBasedProducts, User $user, int $minLimit = 5): Collection
    {
        if ($contentBasedProducts->count() >= $minLimit) {
            return $contentBasedProducts;
        }

        // Lấy các sản phẩm đã xem gần đây từ bảng product_views
        $viewedProductIds = ProductView::where('user_id', $user->id)
            ->orderBy('viewed_at', 'desc')
            ->limit(20)
            ->pluck('product_id')
            ->unique()
            ->toArray();

        if (empty($viewedProductIds)) {
            return $contentBasedProducts;
        }

        // Lấy sản phẩm từ view history (loại trừ các sản phẩm đã có)
        $existingIds = $contentBasedProducts->pluck('id')->toArray();
        $needed = $minLimit - $contentBasedProducts->count();

        $viewHistoryProducts = Product::where('is_active', true)
            ->whereIn('id', $viewedProductIds)
            ->whereNotIn('id', $existingIds)
            ->with([
                'images',
                'category',
                'classifications',
                'variants' => function($q) {
                    $q->where('is_active', true);
                }
            ])
            ->withCount(['reviews as approved_reviews_count' => function($q) {
                $q->where('is_approved', true);
            }])
            ->withAvg(['reviews as avg_rating' => function($q) {
                $q->where('is_approved', true);
            }], 'rating')
            ->selectRaw('products.*, (SELECT COALESCE(SUM(order_items.quantity), 0) FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE order_items.product_id = products.id AND orders.status IN ("processing", "shipped", "delivered")) as sales_count')
            ->limit($needed)
            ->get();

        // Kết hợp và trả về
        return $contentBasedProducts->merge($viewHistoryProducts);
    }

    /**
     * Hàm tổng hợp: Content-Based + View History
     */
    public function getRecommendations(User $user, int $limit = 12): Collection
    {
        // Thực hiện Content-Based Filtering
        $contentBasedProducts = $this->getContentBasedRecommendations($user, $limit);

        // Bổ sung View History nếu cần
        $recommendations = $this->supplementWithViewHistory($contentBasedProducts, $user, 5);

        // Đảm bảo không vượt quá limit
        return $recommendations->take($limit);
    }
}

