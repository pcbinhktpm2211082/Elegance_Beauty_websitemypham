@props(['product'])

<a href="{{ route('user.products.show', $product->id) }}" style="text-decoration: none; color: inherit; display: block;">
    <div class="product-card" style="cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" 
         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">
    @php
        $cover = $product->coverOrFirstImage;
    @endphp

    @if ($cover)
        <img src="{{ asset('storage/' . $cover) }}" 
                 alt="{{ $product->name }}"
                 style="width: 100%; height: 190px; object-fit: cover; border-radius: 12px; margin-bottom: 8px; display: block;">
    @else
        <img src="{{ asset('storage/placeholder.jpg') }}" 
                 alt="Không có ảnh"
                 style="width: 100%; height: 190px; object-fit: cover; border-radius: 12px; margin-bottom: 8px; display: block;">
    @endif

        <h4 style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; text-align: left; line-height: 1.3; min-height: calc(1.3em * 3); margin: 6px 0 4px 0;">{{ $product->name }}</h4>
    
        <div class="product-price-action-wrapper" style="margin-top: 4px;">
            <p class="product-price" style="margin: 0 0 4px 0;">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
        
            {{-- Hiển thị số lượt bán và đánh giá --}}
            <div class="product-rating" style="margin: 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; min-height: 18px;">
                @php
                    $salesCount = $product->sales_count ?? 0;
                    $reviewsCount = $product->approved_reviews_count ?? 0;
                    $avgRating = $product->avg_rating ? round($product->avg_rating, 1) : 0;
                @endphp
                
                {{-- Số lượt bán --}}
                @if($salesCount > 0)
                    <span style="font-size: 12px; color: #6b7280;">Đã bán: <strong style="color: #374151;">{{ number_format($salesCount) }}</strong></span>
                @endif
                
                {{-- Đánh giá -- luôn hiển thị để tránh layout bị nhảy --}}
                @if($reviewsCount > 0)
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <span style="color: #fbbf24; font-size: 14px;">★</span>
                            <span style="font-size: 13px; font-weight: 600; color: #374151;">{{ number_format($avgRating, 1) }}</span>
                        </div>
                        <span style="font-size: 12px; color: #6b7280;">({{ $reviewsCount }} đánh giá)</span>
                    </div>
                @else
                    <span style="font-size: 12px; color: #9ca3af; font-style: italic;">Chưa có đánh giá</span>
                @endif
        </div>
    </div>
</div>
</a>
