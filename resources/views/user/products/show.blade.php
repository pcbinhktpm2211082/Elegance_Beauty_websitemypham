@extends('layouts.user')

@section('title', $product->name)

@section('content')
{{-- Hiển thị thông báo lỗi --}}
@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="product-detail-container">
    <div class="product-detail-wrapper">
        <!-- Phần hình ảnh bên trái -->
        <div class="product-images-section">
            <div class="main-image-container">
                @php
                    $cover = $product->coverOrFirstImage;
                    $images = $product->images;
                    $variantsWithImages = $product->variants->where('image', '!=', null);
                @endphp
                
                @if ($cover)
                    <img id="main-image" src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" class="main-product-image">
                @else
                    <img id="main-image" src="{{ asset('storage/placeholder.jpg') }}" alt="Không có ảnh" class="main-product-image">
                @endif
                
                <!-- Nút điều hướng slide -->
                <button class="slide-nav prev-btn" onclick="changeImage(-1)">&#10094;</button>
                <button class="slide-nav next-btn" onclick="changeImage(1)">&#10095;</button>
            </div>
            
            <!-- Thumbnail images -->
            <div class="thumbnail-container" id="thumbnail-container">
                @if ($cover)
                    <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name }}" class="thumbnail active" onclick="setMainImage(this, '{{ asset('storage/' . $cover) }}')">
                @endif
                
                @foreach($images as $image)
                    @if($image->image_path != $cover)
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="thumbnail" onclick="setMainImage(this, '{{ asset('storage/' . $image->image_path) }}')">
                    @endif
                @endforeach
                
                {{-- Thêm ảnh biến thể vào cuối slide --}}
                @foreach($variantsWithImages as $variant)
                    <img src="{{ asset('storage/' . $variant->image) }}" 
                         alt="{{ $variant->variant_name }}" 
                         class="thumbnail variant-thumbnail" 
                         data-variant-id="{{ $variant->id }}"
                         onclick="setMainImage(this, '{{ asset('storage/' . $variant->image) }}')">
                @endforeach
            </div>
        </div>
        
        <!-- Phần thông tin sản phẩm bên phải -->
        <div class="product-info-section">
            <div class="product-header">
                <h1 class="product-title">{{ $product->name }}</h1>
                <div class="product-rating">
                    <span class="stars">★★★★☆</span>
                    <span class="rating-text">(4.0/5.0)</span>
                </div>
            </div>
            
            <div class="product-price">
                <span class="current-price" id="current-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                @if($product->original_price && $product->original_price > $product->price)
                    <span class="original-price" id="original-price">{{ number_format($product->original_price, 0, ',', '.') }} VNĐ</span>
                    <span class="discount-badge" id="discount-badge">-{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%</span>
                @endif
            </div>
            
            <div class="product-variants">
                <h3>Phân loại sản phẩm</h3>
                @if($product->variants->isNotEmpty())
                    <p class="variant-required">⚠️ <strong>Bắt buộc:</strong> Vui lòng chọn một sản phẩm trước khi thêm vào giỏ hàng</p>
                    
                    <!-- Thông báo variant đang được chọn -->
                    <div id="selected-variant-info" class="selected-variant-info" style="display: none;">
                        <p>✅ <strong>Đang chọn:</strong> <span id="selected-variant-name"></span></p>
                    </div>
                    
                    <div class="variant-options">
                        @foreach($product->variants as $variant)
                            <div class="variant-item">
                                <button class="variant-btn" 
                                        data-variant-id="{{ $variant->id }}"
                                        data-price="{{ $variant->price }}"
                                        data-quantity="{{ $variant->quantity }}"
                                        @if($variant->image) data-image="{{ asset('storage/' . $variant->image) }}" @endif
                                        onclick="selectVariant(this)">
                                    @if($variant->image)
                                        <img src="{{ asset('storage/' . $variant->image) }}" 
                                             alt="{{ $variant->variant_name }}" 
                                             class="variant-thumbnail">
                                    @endif
                                    <span class="variant-name">{{ $variant->variant_name }}</span>
                                    @if($variant->price)
                                        <span class="variant-price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</span>
                                    @endif
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div class="product-quantity">
                <label>Số lượng:</label>
                <div class="quantity-selector">
                    <button class="qty-btn" onclick="changeQuantity(-1)">-</button>
                    <input type="number" id="quantity" value="1" min="1" max="99">
                    <button class="qty-btn" onclick="changeQuantity(1)">+</button>
                </div>
            </div>
            
            <div class="product-actions">
                <button class="action-btn add-to-cart-btn" onclick="addToCart()">
                    <i class="fas fa-shopping-cart"></i>
                    Thêm vào giỏ hàng
                </button>
                <button class="action-btn buy-now-btn" onclick="buyNow()">
                    <i class="fas fa-bolt"></i>
                    Mua ngay
                </button>
            </div>
            
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Tình trạng:</span>
                    <span class="meta-value in-stock" id="stock-status">
                        @if($product->variants->isNotEmpty())
                            @php
                                $totalQuantity = $product->variants->sum('quantity');
                            @endphp
                            @if($totalQuantity > 0)
                                Còn hàng ({{ $totalQuantity }})
                            @else
                                Hết hàng
                            @endif
                        @else
                            @if($product->quantity > 0)
                                Còn hàng ({{ $product->quantity }})
                            @else
                                Hết hàng
                            @endif
                        @endif
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Mã sản phẩm:</span>
                    <span class="meta-value">{{ $product->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Danh mục:</span>
                    <span class="meta-value">{{ $product->category->name ?? 'Không phân loại' }}</span>
                </div>
            </div>
            
            {{-- Phân loại sản phẩm --}}
            @if($product->classifications->isNotEmpty())
                <div class="product-classifications" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <span class="meta-label" style="display: block; margin-bottom: 10px; font-weight: 600;">Phân loại:</span>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($product->classifications as $classification)
                            <span class="classification-tag" style="display: inline-block; padding: 6px 12px; background: {{ $classification->type == 'skin_type' ? '#dbeafe' : '#fef3c7' }}; color: {{ $classification->type == 'skin_type' ? '#1e40af' : '#92400e' }}; border-radius: 6px; font-size: 13px; font-weight: 500;">
                                {{ $classification->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sản phẩm khác cùng mục đích (Routine) -->
    <div class="routine-recommendations-section" style="margin-top: 60px; padding: 40px 20px; background: #ffffff; border-top: 2px solid #e5e7eb;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 10px; color: #1f2937; text-align: center;">
                🧴 Sản phẩm khác cùng mục đích (Routine)
            </h2>
            <p style="text-align: center; color: #6b7280; margin-bottom: 30px; font-size: 14px;">
                Hoàn thiện quy trình chăm sóc da của bạn
            </p>
            <div id="routine-products-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    Đang tải gợi ý...
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phần mô tả sản phẩm -->
    <div class="product-description-section">
        <div class="description-tabs">
            <button class="tab-btn active" onclick="showTab('description')">Mô tả</button>
            <button class="tab-btn" onclick="showTab('reviews')">Đánh giá</button>
        </div>
        
        <div class="tab-content active" id="description">
            <h3>Mô tả sản phẩm</h3>
            <div class="description-content">
                {!! $product->description !!}
            </div>
        </div>
        
        <div class="tab-content" id="reviews">
            <h3>Đánh giá sản phẩm</h3>
            <div class="reviews-content">
                @php
                    $approvedReviews = $product->reviews()->with(['user','images'])
                        ->where('is_approved', true)
                        ->latest()
                        ->get();
                    $avgRating = $approvedReviews->avg('rating');
                @endphp

                @if($approvedReviews->isNotEmpty())
                    <div class="reviews-summary" style="display:flex; align-items:center; gap:16px; padding:14px 16px; border-radius:12px; background:linear-gradient(135deg,#fef3c7,#fffbeb); border:1px solid #fde68a; margin-bottom:16px;">
                        <div class="summary-left" style="display:flex; align-items:center; gap:14px;">
                            <div class="avg-score" style="display:flex; flex-direction:column; align-items:flex-start;">
                                <span class="avg-number" style="font-size:22px; font-weight:700; color:#b45309;">{{ number_format($avgRating, 1) }}</span>
                                <span class="avg-text" style="font-size:12px; color:#92400e;">/ 5.0</span>
                            </div>
                            <div>
                                <div class="avg-stars" style="color:#fbbf24; font-size:18px; letter-spacing:1px;">
                                    @for($i=1;$i<=5;$i++)
                                        @if($avgRating >= $i)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                                <p class="total-reviews" style="font-size:13px; color:#6b7280; margin-top:2px;">
                                    {{ $approvedReviews->count() }} đánh giá đã mua hàng
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-list" style="display:flex; flex-direction:column; gap:14px;">
                        @foreach($approvedReviews as $review)
                            <div class="review-item" style="padding:12px 14px; border-radius:12px; border:1px solid #e5e7eb; background:#ffffff;">
                                <div class="review-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <strong class="review-user" style="font-size:13px; color:#111827;">
                                        {{ $review->user->name ?? 'Người dùng ẩn danh' }}
                                    </strong>
                                    <span class="review-date" style="font-size:12px; color:#9ca3af;">
                                        {{ $review->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="review-stars" style="color:#fbbf24; font-size:14px; margin-bottom:6px;">
                                    @for($i=1;$i<=5;$i++)
                                        <span class="{{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}">★</span>
                                    @endfor
                                </div>
                                @if($review->comment)
                                    <p class="review-comment" style="font-size:13px; color:#374151; line-height:1.5; margin-bottom:8px;">
                                        {{ $review->comment }}
                                    </p>
                                @endif
                                @if($review->images->isNotEmpty())
                                    <div class="review-images" style="display:flex; flex-wrap:wrap; gap:6px;">
                                        @foreach($review->images as $image)
                                            <img src="{{ asset('storage/'.$image->image_path) }}"
                                                 alt="Ảnh đánh giá"
                                                 class="review-image-thumb"
                                                 onclick="openReviewImageModal('{{ asset('storage/'.$image->image_path) }}')"
                                                 style="width:68px; height:68px; object-fit:cover; border-radius:8px; cursor:pointer; border:1px solid #e5e7eb;">
                                        @endforeach
                                    </div>
                                @endif
                                @if($review->admin_reply)
                                    <div class="review-admin-reply" style="margin-top:8px; padding:8px 10px; border-radius:10px; background:#f9fafb; border:1px solid #e5e7eb;">
                                        <div style="font-size:12px; font-weight:600; color:#111827; margin-bottom:2px;">
                                            Phản hồi từ cửa hàng
                                            @if($review->admin_replied_at)
                                                @php
                                                    $repliedAt = $review->admin_replied_at instanceof \Illuminate\Support\Carbon
                                                        ? $review->admin_replied_at
                                                        : \Illuminate\Support\Carbon::parse($review->admin_replied_at);
                                                @endphp
                                                <span style="font-size:11px; color:#9ca3af; font-weight:400; margin-left:4px;">
                                                    ({{ $repliedAt->format('d/m/Y H:i') }})
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size:13px; color:#374151;">
                                            {{ $review->admin_reply }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:13px; color:#6b7280; padding:10px 0;">Sản phẩm chưa có đánh giá.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal xem ảnh đánh giá -->
<div id="review-image-modal"
     style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div id="review-image-modal-box"
         style="position:relative; max-width:90%; max-height:90%; margin:0 16px;">
        <button type="button"
                onclick="closeReviewImageModal()"
                style="position:absolute; top:-32px; right:0; border:none; background:none; cursor:pointer; color:#f9fafb; font-size:22px;">
            ✕
        </button>
        <img id="review-image-modal-img"
             src=""
             alt="Ảnh đánh giá"
             style="max-width:100%; max-height:100%; border-radius:12px; box-shadow:0 20px 50px rgba(15,23,42,0.6); background:#111827;">
    </div>
</div>

<script>
let currentImageIndex = 0;
let productImages = [];
let variantImages = [];
let allImages = [];
let selectedVariant = null;
let basePrice = {{ $product->price }};
let baseOriginalPrice = {{ $product->original_price ?? 0 }};
const productHasVariants = {{ $product->variants->count() }};

// Khởi tạo danh sách ảnh
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    // Tách ảnh sản phẩm và ảnh biến thể
    productImages = Array.from(thumbnails).filter(img => !img.classList.contains('variant-thumbnail')).map(img => img.src);
    variantImages = Array.from(thumbnails).filter(img => img.classList.contains('variant-thumbnail')).map(img => img.src);
    
    // Tạo danh sách tất cả ảnh: ảnh sản phẩm trước, ảnh biến thể sau
    allImages = [...productImages, ...variantImages];
    
    console.log('Product images:', productImages);
    console.log('Variant images:', variantImages);
    console.log('All images:', allImages);
});

// Modal xem ảnh đánh giá
function openReviewImageModal(src) {
    const modal = document.getElementById('review-image-modal');
    const img   = document.getElementById('review-image-modal-img');
    if (!modal || !img) return;
    img.src = src;
    modal.style.display = 'flex';
}

function closeReviewImageModal() {
    const modal = document.getElementById('review-image-modal');
    if (modal) modal.style.display = 'none';
}

function selectVariant(variantBtn) {
    console.log('Selecting variant:', variantBtn.dataset);
    
    // Bỏ active tất cả variant buttons
    document.querySelectorAll('.variant-btn').forEach(btn => btn.classList.remove('active'));
    
    // Active variant button được chọn
    variantBtn.classList.add('active');
    
    // Hiển thị thông báo variant đang được chọn
    const selectedVariantInfo = document.getElementById('selected-variant-info');
    const selectedVariantName = document.getElementById('selected-variant-name');
    if (selectedVariantInfo && selectedVariantName) {
        selectedVariantName.textContent = variantBtn.querySelector('.variant-name').textContent;
        selectedVariantInfo.style.display = 'block';
    }
    
    // Lấy thông tin biến thể
    const variantId = variantBtn.dataset.variantId;
    const variantPrice = variantBtn.dataset.price;
    const variantImage = variantBtn.dataset.image;
    const variantQuantity = parseInt(variantBtn.dataset.quantity) || 0;
    
    selectedVariant = {
        id: variantId,
        price: variantPrice,
        image: variantImage,
        quantity: variantQuantity
    };
    
    // Cập nhật giá nếu biến thể có giá riêng
    if (variantPrice && variantPrice > 0) {
        document.getElementById('current-price').textContent = Number(variantPrice).toLocaleString('vi-VN') + ' VNĐ';
        
        // Cập nhật discount nếu có
        if (baseOriginalPrice > variantPrice) {
            const discount = Math.round(((baseOriginalPrice - variantPrice) / baseOriginalPrice) * 100);
            document.getElementById('original-price').textContent = Number(baseOriginalPrice).toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('discount-badge').textContent = '-' + discount + '%';
        }
    } else {
        // Reset về giá gốc
        document.getElementById('current-price').textContent = Number(basePrice).toLocaleString('vi-VN') + ' VNĐ';
        if (baseOriginalPrice > basePrice) {
            const discount = Math.round(((baseOriginalPrice - basePrice) / baseOriginalPrice) * 100);
            document.getElementById('original-price').textContent = Number(baseOriginalPrice).toLocaleString('vi-VN') + ' VNĐ';
            document.getElementById('discount-badge').textContent = '-' + discount + '%';
        }
    }
    
    // Cập nhật tình trạng hàng
    const stockStatus = document.getElementById('stock-status');
    if (variantQuantity > 0) {
        stockStatus.textContent = `Còn hàng (${variantQuantity})`;
        stockStatus.className = 'meta-value in-stock';
    } else {
        stockStatus.textContent = 'Hết hàng';
        stockStatus.className = 'meta-value out-of-stock';
    }
    
    // Cập nhật ảnh nếu biến thể có ảnh riêng
    if (variantImage) {
        // Cập nhật ảnh chính thành ảnh của variant
        document.getElementById('main-image').src = variantImage;
        
        // Cập nhật thumbnail active cho ảnh variant
        document.querySelectorAll('.thumbnail').forEach((thumb) => {
            if (thumb.src === variantImage) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });
        
        // Tìm index của ảnh variant trong danh sách tất cả ảnh
        currentImageIndex = allImages.indexOf(variantImage);
        if (currentImageIndex === -1) {
            currentImageIndex = 0; // Fallback nếu không tìm thấy
        }
        
        // Highlight ảnh của variant được chọn
        highlightVariantImages(variantId);
    } else {
        // Nếu variant không có ảnh riêng, hiển thị ảnh đầu tiên của sản phẩm
        if (productImages.length > 0) {
            document.getElementById('main-image').src = productImages[0];
            currentImageIndex = 0;
            
            // Cập nhật thumbnail active cho ảnh đầu tiên
            document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
                if (index === 0) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }
        
        // Reset highlight cho tất cả ảnh
        resetVariantImageHighlight();
        
        // Ẩn thông báo variant đang được chọn
        const selectedVariantInfo = document.getElementById('selected-variant-info');
        if (selectedVariantInfo) {
            selectedVariantInfo.style.display = 'none';
        }
    }
}

function setMainImage(thumbnail, imageSrc) {
    // Cập nhật ảnh chính
    document.getElementById('main-image').src = imageSrc;
    
    // Cập nhật trạng thái active của thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    thumbnail.classList.add('active');
    
    // Cập nhật index hiện tại
    currentImageIndex = allImages.indexOf(imageSrc);
}

function changeImage(direction) {
    if (allImages.length === 0) return;
    
    currentImageIndex += direction;
    
    if (currentImageIndex >= allImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = allImages.length - 1;
    }
    
    const newImageSrc = allImages[currentImageIndex];
    document.getElementById('main-image').src = newImageSrc;
    
    // Cập nhật thumbnail active
    document.querySelectorAll('.thumbnail').forEach((thumb, index) => {
        if (thumb.src === newImageSrc) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newValue = parseInt(quantityInput.value) + change;
    
    if (newValue < 1) newValue = 1;
    if (newValue > 99) newValue = 99;
    
    quantityInput.value = newValue;
}

// Highlight ảnh của variant được chọn
function highlightVariantImages(variantId) {
    // Reset tất cả highlight trước
    resetVariantImageHighlight();
    
    // Highlight ảnh của variant được chọn
    const variantThumbnails = document.querySelectorAll(`.variant-thumbnail[data-variant-id="${variantId}"]`);
    variantThumbnails.forEach(thumb => {
        thumb.classList.add('selected');
    });
}

// Reset highlight cho tất cả ảnh
function resetVariantImageHighlight() {
    const allThumbnails = document.querySelectorAll('.thumbnail');
    allThumbnails.forEach(thumb => {
        thumb.classList.remove('selected');
    });
}

function showTab(tabName) {
    // Ẩn tất cả tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Bỏ active tất cả tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Hiển thị tab được chọn
    document.getElementById(tabName).classList.add('active');
    
    // Active tab button được chọn
    event.target.classList.add('active');
}

async function addToCart() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const productId = {{ $product->id }};
    const variantId = selectedVariant ? selectedVariant.id : null;
    const hasVariants = productHasVariants;
    
    // Validate quantity
    if (quantity < 1 || quantity > 99) {
        showNotification('Số lượng không hợp lệ!', 'error');
        return;
    }
    
    // Check if product has variants and variant is selected
    if (hasVariants > 0 && !variantId) {
        showNotification('Vui lòng chọn phân loại sản phẩm trước khi thêm vào giỏ hàng!', 'error');
        return false;
    }
    
    // Check stock
    const stockStatus = document.getElementById('stock-status').textContent;
    if (stockStatus.includes('Hết hàng')) {
        showNotification('Sản phẩm đã hết hàng!', 'error');
        return false;
    }
    
    // Prepare data using JSON
    const data = {
        product_id: productId,
        quantity: quantity,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    if (variantId) {
        data.variant_id = variantId;
    }
    
    console.log('Adding to cart:', {
        productId: productId,
        variantId: variantId,
        quantity: quantity,
        hasVariants: hasVariants
    });
    
    console.log('Request data:', data);
    
    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type') || '';
        const payload = contentType.includes('application/json') ? await response.json() : null;
        if (payload && payload.success) {
            showNotification(payload.message, 'success');
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                const count = Number(payload.cart_count || 0);
                cartCountElement.textContent = count;
                cartCountElement.style.display = count === 0 ? 'none' : 'block';
            }
            return true;
        }
        showNotification((payload && payload.message) || 'Có lỗi xảy ra!', 'error');
        return false;
    } catch (error) {
        showNotification('Có lỗi xảy ra: ' + error.message, 'error');
        return false;
    }
}

async function buyNow() {
    // Bắt buộc chọn phân loại nếu có
    if (productHasVariants > 0 && !selectedVariant) {
        showNotification('Vui lòng chọn phân loại sản phẩm trước khi mua ngay!', 'error');
        return;
    }
    const ok = await addToCart();
    if (ok) {
        window.location.href = '{{ route("cart.index") }}';
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '5px';
    notification.style.color = 'white';
    notification.style.fontWeight = 'bold';
    notification.style.zIndex = '9999';
    notification.style.animation = 'slideIn 0.3s ease';
    
    if (type === 'success') {
        notification.style.backgroundColor = '#4CAF50';
    } else {
        notification.style.backgroundColor = '#f44336';
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
    }
</script>

<!-- Phần gợi ý sản phẩm -->
<div class="recommendations-section" style="margin-top: 60px; padding: 40px 20px; background: #f9fafb;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div class="recommendations-tabs" style="display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb;">
            <button class="rec-tab-btn active" onclick="loadRecommendations('content-based', this)" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid #3b82f6; color: #3b82f6; font-weight: 600; cursor: pointer; font-size: 16px;">
                💡 Gợi ý theo loại da
            </button>
            <button class="rec-tab-btn" onclick="loadRecommendations('view-history', this)" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: #6b7280; font-weight: 500; cursor: pointer; font-size: 16px;">
                📚 Dựa trên lịch sử xem
            </button>
            <button class="rec-tab-btn" onclick="loadRecommendations('hybrid', this)" style="padding: 12px 24px; background: none; border: none; border-bottom: 3px solid transparent; color: #6b7280; font-weight: 500; cursor: pointer; font-size: 16px;">
                ⭐ Gợi ý tổng hợp
            </button>
        </div>
        
        <div id="recommendations-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                Đang tải gợi ý...
            </div>
        </div>
    </div>
</div>

<script>
async function loadRecommendations(type, buttonElement) {
    // Update active tab
    document.querySelectorAll('.rec-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.borderBottomColor = 'transparent';
        btn.style.color = '#6b7280';
        btn.style.fontWeight = '500';
    });
    
    buttonElement.classList.add('active');
    buttonElement.style.borderBottomColor = '#3b82f6';
    buttonElement.style.color = '#3b82f6';
    buttonElement.style.fontWeight = '600';
    
    const container = document.getElementById('recommendations-container');
    container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Đang tải...</div>';
    
    try {
        const response = await fetch(`/recommendations/${type}?limit=8`);
        const data = await response.json();
        
        if (data.success && data.products && data.products.length > 0) {
            container.innerHTML = data.products.map(product => {
                const image = product.images && product.images.length > 0 
                    ? `/storage/${product.images[0].image_path}` 
                    : '/storage/placeholder.jpg';
                const price = new Intl.NumberFormat('vi-VN').format(product.price);
                
                return `
                    <div class="product-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;" 
                         onclick="window.location.href='/products/${product.id}'"
                         onmouseover="this.style.transform='translateY(-5px)'"
                         onmouseout="this.style.transform='translateY(0)'">
                        <img src="${image}" alt="${product.name}" style="width: 100%; height: 200px; object-fit: cover;">
                        <div style="padding: 15px;">
                            <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #1f2937; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${product.name}</h3>
                            <p style="font-size: 16px; font-weight: 700; color: #3b82f6; margin: 0;">${price} VNĐ</p>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Không có sản phẩm gợi ý</div>';
        }
    } catch (error) {
        console.error('Error loading recommendations:', error);
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: #f44336;">Có lỗi xảy ra khi tải gợi ý</div>';
    }
}

// Load content-based recommendations by default
document.addEventListener('DOMContentLoaded', function() {
    loadRecommendations('content-based', document.querySelector('.rec-tab-btn.active'));
    
    // Load routine recommendations
    loadRoutineRecommendations();
});

// Load routine recommendations
async function loadRoutineRecommendations() {
    const container = document.getElementById('routine-products-container');
    const productId = {{ $product->id }};
    
    try {
        const response = await fetch(`/recommendations/routine/${productId}?limit=6`);
        const data = await response.json();
        
        if (data.success && data.products && data.products.length > 0) {
            container.innerHTML = data.products.map(product => {
                const image = product.images && product.images.length > 0 
                    ? `/storage/${product.images[0].image_path}` 
                    : '/storage/placeholder.jpg';
                const price = new Intl.NumberFormat('vi-VN').format(product.price);
                
                return `
                    <div class="product-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer; border: 2px solid #e5e7eb;" 
                         onclick="window.location.href='/products/${product.id}'"
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='#3b82f6';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e5e7eb';">
                        <img src="${image}" alt="${product.name}" style="width: 100%; height: 200px; object-fit: cover;">
                        <div style="padding: 15px;">
                            <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #1f2937; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${product.name}</h3>
                            <p style="font-size: 16px; font-weight: 700; color: #3b82f6; margin: 0;">${price} VNĐ</p>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Không có sản phẩm gợi ý</div>';
        }
    } catch (error) {
        console.error('Error loading routine recommendations:', error);
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Không thể tải gợi ý</div>';
    }
}
</script>
@endsection
