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
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            </div>
        </div>
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
@endsection
