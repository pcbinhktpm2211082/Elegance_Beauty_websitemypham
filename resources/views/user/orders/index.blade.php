@extends('layouts.user')

@section('title', 'Đơn hàng của tôi')

@push('styles')
<style>
    /* Giảm khoảng cách để trang không quá dài */
    .orders-list {
        gap: 15px !important;
    }
    
    .order-card {
        padding: 18px 20px !important;
    }
    
    .order-header {
        margin-bottom: 15px !important;
        padding-bottom: 12px !important;
    }
    
    .order-summary {
        gap: 20px !important;
        margin-bottom: 15px !important;
    }
    
    .order-items h4 {
        margin: 0 0 10px 0 !important;
        font-size: 0.95rem !important;
    }
    
    .items-preview {
        gap: 8px !important;
    }
    
    .item-preview {
        padding: 8px !important;
        gap: 10px !important;
    }
    
    .item-image {
        width: 45px !important;
        height: 45px !important;
    }
    
    .item-info {
        font-size: 0.9rem !important;
    }
    
    .item-name {
        margin: 0 0 4px 0 !important;
        font-size: 0.9rem !important;
        line-height: 1.3 !important;
    }
    
    .item-quantity {
        margin: 0 !important;
        font-size: 0.85rem !important;
    }
    
    .order-total {
        gap: 8px !important;
    }
    
    .total-item {
        margin-bottom: 6px !important;
        font-size: 0.9rem !important;
    }
    
    .order-actions {
        gap: 10px !important;
        margin-top: 12px !important;
    }
</style>
@endpush

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>Đơn hàng của tôi</h1>
        <p>Theo dõi và quản lý các đơn hàng của bạn</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-container">
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-shopping-bag"></i> Danh sách đơn hàng</h2>
                <a href="{{ route('profile.show') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            @if($orders->count() > 0)
                <div class="orders-list">
                    @foreach($orders as $order)
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Đơn hàng #{{ $order->id }}</h3>
                                    <span class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-{{ $order->status }}">
                                        @switch($order->status)
                                            @case('pending')
                                                Chờ xử lý
                                                @break
                                            @case('processing')
                                                Đang xử lý
                                                @break
                                            @case('shipped')
                                                Đang giao hàng
                                                @break
                                            @case('delivered')
                                                Đã hoàn thành
                                                @break
                                            @case('cancelled')
                                                Đã hủy
                                                @break
                                            @default
                                                {{ ucfirst($order->status) }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-summary">
                                <div class="order-items">
                                    <h4>Sản phẩm:</h4>
                                    <div class="items-preview" id="items-preview-{{ $order->id }}">
                                        @foreach($order->orderItems as $index => $item)
                                            <div class="item-preview {{ $index >= 3 ? 'item-hidden' : '' }}" 
                                                 data-order-id="{{ $order->id }}"
                                                 style="{{ $index >= 3 ? 'display: none;' : '' }}">
                                                @php
                                                    $product = $item->product;
                                                    $cover = $product ? $product->coverOrFirstImage : null;
                                                @endphp
                                                @if($cover)
                                                    <img src="{{ asset('storage/' . $cover) }}"
                                                         alt="{{ $product->name }}"
                                                         class="item-image">
                                                @else
                                                    <img src="{{ asset('storage/placeholder.jpg') }}"
                                                         alt="Không có ảnh"
                                                         class="item-image">
                                                @endif
                                                <div class="item-info">
                                                    <p class="item-name">{{ $product->name ?? $item->product_name }}</p>
                                                    <p class="item-quantity">Số lượng: {{ $item->quantity }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 3)
                                            <div class="more-items" 
                                                 id="more-items-{{ $order->id }}"
                                                 onclick="toggleOrderItems({{ $order->id }}, {{ $order->orderItems->count() }})"
                                                 style="cursor: pointer; padding: 8px 12px; text-align: center; background: #8b5d33; border-radius: 8px; margin-top: 6px; transition: all 0.2s;"
                                                 onmouseover="this.style.background='#6a4625'; this.style.transform='translateY(-1px)'"
                                                 onmouseout="this.style.background='#8b5d33'; this.style.transform='translateY(0)'">
                                                <span style="color: #ffffff; font-weight: 500; font-size: 0.9rem;">+{{ $order->orderItems->count() - 3 }} sản phẩm khác</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="order-total">
                                    <div class="total-item">
                                        <span>Tổng tiền:</span>
                                        <strong>{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</strong>
                                    </div>
                                    <div class="total-item">
                                        <span>Phương thức thanh toán:</span>
                                        <span>{{ $order->payment_method_text }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="{{ route('orders.show', $order) }}" class="view-order-btn">
                                    <i class="fas fa-eye"></i>
                                    Xem chi tiết
                                </a>
                                @if($order->status === 'delivered')
                                    <button type="button"
                                            class="review-order-btn"
                                            style="border:none; border-radius:999px; padding:8px 14px; background:linear-gradient(135deg,#b45309,#92400e); color:#fff; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
                                            data-order-id="{{ $order->id }}"
                                            onclick="openOrderReviewModal({{ $order->id }})">
                                        <i class="fas fa-star"></i>
                                        Đánh giá
                                    </button>
                                @elseif($order->status === 'pending')
                                    <button class="cancel-order-btn" onclick="cancelOrder({{ $order->id }})">
                                        <i class="fas fa-times"></i>
                                        Hủy đơn hàng
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="pagination-container">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Bạn chưa có đơn hàng nào</h3>
                    <p>Hãy mua sắm để có đơn hàng đầu tiên!</p>
                    <a href="{{ route('products.index') }}" class="shop-now-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal đánh giá sản phẩm từ đơn hàng -->
<div id="order-review-modal"
     data-review-base-url="{{ url('/products') }}"
     onclick="if(event.target === this) closeOrderReviewModal()"
     style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center; overflow-y:auto;">
    <div id="order-review-modal-box"
         style="position:relative; max-width:700px; width:100%; margin:20px 16px; background:#ffffff; border-radius:16px; box-shadow:0 20px 50px rgba(15,23,42,0.35); padding:24px; max-height:90vh; overflow-y:auto;">
        <button type="button"
                onclick="closeOrderReviewModal()"
                style="position:absolute; top:10px; right:14px; border:none; background:none; cursor:pointer; color:#6b7280; font-size:20px; z-index:10;">
            ✕
        </button>
        <h3 style="font-size:20px; font-weight:600; margin-bottom:8px; color:#111827;">
            Đánh giá sản phẩm trong đơn hàng
        </h3>
        <p style="font-size:13px; color:#6b7280; margin-bottom:20px;">
            Vui lòng đánh giá các sản phẩm bạn đã mua. Mỗi sản phẩm chỉ được đánh giá một lần.
        </p>

        <!-- Danh sách sản phẩm chưa đánh giá -->
        <div id="unreviewed-products-list" style="margin-bottom:20px;">
            <div style="text-align:center; padding:40px 20px; color:#9ca3af;">
                <i class="fas fa-spinner fa-spin" style="font-size:24px; margin-bottom:10px;"></i>
                <p>Đang tải danh sách sản phẩm...</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOrderItems(orderId, totalItems) {
    const previewContainer = document.getElementById('items-preview-' + orderId);
    const moreItemsBtn = document.getElementById('more-items-' + orderId);
    
    if (!previewContainer || !moreItemsBtn) return;
    
    // Lấy tất cả các item preview của order này
    const allItems = previewContainer.querySelectorAll('.item-preview[data-order-id="' + orderId + '"]');
    const hiddenItems = Array.from(allItems).slice(3); // Từ item thứ 4 trở đi
    
    if (hiddenItems.length === 0) return;
    
    // Kiểm tra xem đang ẩn hay hiện (kiểm tra item đầu tiên trong hiddenItems)
    const isCurrentlyHidden = hiddenItems[0].style.display === 'none' || hiddenItems[0].classList.contains('item-hidden');
    
        if (isCurrentlyHidden) {
        // Hiển thị tất cả sản phẩm
        hiddenItems.forEach(item => {
            item.style.display = 'flex';
            item.classList.remove('item-hidden');
        });
        moreItemsBtn.innerHTML = '<span style="color: #ffffff; font-weight: 500; font-size: 0.9rem;">Ẩn bớt sản phẩm</span>';
    } else {
        // Ẩn các sản phẩm từ thứ 4 trở đi
        hiddenItems.forEach(item => {
            item.style.display = 'none';
            item.classList.add('item-hidden');
        });
        moreItemsBtn.innerHTML = '<span style="color: #ffffff; font-weight: 500; font-size: 0.9rem;">+' + (totalItems - 3) + ' sản phẩm khác</span>';
    }
}

function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đã hủy đơn hàng thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy đơn hàng.');
        });
    }
}

let currentOrderId = null;

function openOrderReviewModal(orderId) {
    const modal = document.getElementById('order-review-modal');
    const listContainer = document.getElementById('unreviewed-products-list');
    
    if (!modal || !listContainer) return;
    
    currentOrderId = orderId;
    modal.style.display = 'flex';
    
    // Load danh sách sản phẩm chưa đánh giá
    loadUnreviewedProducts(orderId);
}

function loadUnreviewedProducts(orderId) {
    const listContainer = document.getElementById('unreviewed-products-list');
    if (!listContainer) return;
    
    listContainer.innerHTML = `
        <div style="text-align:center; padding:40px 20px; color:#9ca3af;">
            <i class="fas fa-spinner fa-spin" style="font-size:24px; margin-bottom:10px;"></i>
            <p>Đang tải danh sách sản phẩm...</p>
        </div>
    `;
    
    fetch(`/orders/${orderId}/unreviewed-products`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.products.length > 0) {
            renderUnreviewedProducts(data.products);
        } else {
            listContainer.innerHTML = `
                <div style="text-align:center; padding:40px 20px; color:#9ca3af;">
                    <i class="fas fa-check-circle" style="font-size:48px; color:#10b981; margin-bottom:15px;"></i>
                    <p style="font-size:16px; font-weight:500; color:#374151;">Bạn đã đánh giá tất cả sản phẩm trong đơn hàng này!</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading unreviewed products:', error);
        listContainer.innerHTML = `
            <div style="text-align:center; padding:40px 20px; color:#ef4444;">
                <i class="fas fa-exclamation-circle" style="font-size:24px; margin-bottom:10px;"></i>
                <p>Có lỗi xảy ra khi tải danh sách sản phẩm.</p>
            </div>
        `;
    });
}

function renderUnreviewedProducts(products) {
    const listContainer = document.getElementById('unreviewed-products-list');
    if (!listContainer) return;
    
    const baseUrl = document.getElementById('order-review-modal').dataset.reviewBaseUrl;
    
    listContainer.innerHTML = products.map((product, index) => `
        <div class="product-review-item" data-product-id="${product.product_id}" style="border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:16px; background:#f9fafb;">
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <img src="${product.product_image}" alt="${product.product_name}" 
                     style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;">
                <div style="flex:1;">
                    <h4 style="font-size:15px; font-weight:600; color:#111827; margin-bottom:4px;">${product.product_name}</h4>
                    <p style="font-size:13px; color:#6b7280;">Số lượng: ${product.quantity}</p>
                </div>
            </div>
            
            <form class="product-review-form" data-product-id="${product.product_id}" data-product-name="${product.product_name}" 
                  method="POST" action="${baseUrl}/${product.product_id}/reviews" enctype="multipart/form-data" 
                  style="margin-top:12px;">
                @csrf
                <input type="hidden" name="order_id" value="${currentOrderId}">
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:6px; color:#374151;">Số sao:</label>
                    <div class="star-selector" style="display:flex; gap:6px;">
                        ${[1,2,3,4,5].map(i => `
                            <label style="cursor:pointer;">
                                <input type="radio" name="rating" value="${i}" ${i===5 ? 'checked' : ''} style="display:none;">
                                <span data-value="${i}" style="font-size:20px; color:${i <= 5 ? '#fbbf24' : '#d1d5db'}; transition:color 0.2s;">★</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:6px; color:#374151;">Nhận xét:</label>
                    <textarea name="comment" rows="3" 
                              style="width:100%; border-radius:8px; border:1px solid #d1d5db; padding:8px 10px; font-size:13px; resize:vertical;"
                              placeholder="Chất lượng sản phẩm, trải nghiệm sử dụng..."></textarea>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:13px; font-weight:500; margin-bottom:6px; color:#374151;">Ảnh kèm theo (tối đa 5 ảnh):</label>
                    <input type="file" name="images[]" accept="image/*" multiple 
                           style="width:100%; font-size:13px; padding:6px; border:1px solid #d1d5db; border-radius:8px;">
                </div>
                <button type="submit" 
                        style="width:100%; border:none; border-radius:8px; padding:10px 18px; background:linear-gradient(135deg,#b45309,#92400e); color:#fff; font-size:13px; font-weight:600; cursor:pointer; transition:opacity 0.2s;">
                    <i class="fas fa-star"></i> Gửi đánh giá
                </button>
            </form>
        </div>
    `).join('');
    
    // Khởi tạo star selector cho mỗi form
    listContainer.querySelectorAll('.star-selector').forEach(starContainer => {
        const labels = starContainer.querySelectorAll('label');
        labels.forEach(label => {
            label.addEventListener('click', function() {
                const input = this.querySelector('input[type="radio"]');
                const value = parseInt(input.value);
                input.checked = true;
                
                labels.forEach(lb => {
                    const span = lb.querySelector('span');
                    const starVal = parseInt(span.getAttribute('data-value'));
                    span.style.color = starVal <= value ? '#fbbf24' : '#d1d5db';
                });
            });
        });
    });
    
    // Xử lý submit form
    listContainer.querySelectorAll('.product-review-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitProductReview(this);
        });
    });
}

function submitProductReview(form) {
    const productId = form.dataset.productId;
    const productName = form.dataset.productName;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Xóa sản phẩm đã đánh giá khỏi danh sách
            const productItem = form.closest('.product-review-item');
            if (productItem) {
                productItem.style.transition = 'opacity 0.3s, transform 0.3s';
                productItem.style.opacity = '0';
                productItem.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    productItem.remove();
                    // Kiểm tra xem còn sản phẩm nào không
                    const remainingProducts = document.querySelectorAll('.product-review-item');
                    if (remainingProducts.length === 0) {
                        loadUnreviewedProducts(currentOrderId);
                    }
                }, 300);
            }
            
            // Hiển thị thông báo thành công
            showNotification('Đánh giá thành công!', 'success');
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Vui lòng thử lại.'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error submitting review:', error);
        alert('Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function closeOrderReviewModal() {
    const modal = document.getElementById('order-review-modal');
    if (modal) {
        modal.style.display = 'none';
        currentOrderId = null;
    }
}

// Khởi tạo chọn sao cho pop-up đánh giá đơn hàng
document.addEventListener('DOMContentLoaded', function () {
    const starContainer = document.getElementById('order-review-stars');
    if (!starContainer) return;

    const labels = starContainer.querySelectorAll('label');

    labels.forEach((label, index) => {
        label.addEventListener('click', function () {
            const input = this.querySelector('input[type="radio"]');
            const value = parseInt(input.value);

            // Cập nhật checked cho radio
            input.checked = true;

            // Đổi màu các ngôi sao
            labels.forEach((lb) => {
                const span = lb.querySelector('span');
                const starVal = parseInt(span.getAttribute('data-value'));
                if (starVal <= value) {
                    span.style.color = '#fbbf24'; // vàng
                } else {
                    span.style.color = '#d1d5db'; // xám nhạt
                }
            });
        });
    });
});
</script>
@endsection
