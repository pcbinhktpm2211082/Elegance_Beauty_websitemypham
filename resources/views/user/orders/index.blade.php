@extends('layouts.user')

@section('title', 'Đơn hàng của tôi')

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
                                    <div class="items-preview">
                                        @foreach($order->orderItems->take(3) as $item)
                                            <div class="item-preview">
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
                                            <div class="more-items">
                                                <span>+{{ $order->orderItems->count() - 3 }} sản phẩm khác</span>
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
                                    @php $firstProduct = optional($order->orderItems->first())->product; @endphp
                                    @if($firstProduct)
                                        <button type="button"
                                                class="review-order-btn"
                                                style="border:none; border-radius:999px; padding:8px 14px; background:linear-gradient(135deg,#b45309,#92400e); color:#fff; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;"
                                                data-product-id="{{ $firstProduct->id }}"
                                                data-product-name="{{ $firstProduct->name }}"
                                                onclick="openOrderReviewModal(this.dataset.productId, this.dataset.productName)">
                                            <i class="fas fa-star"></i>
                                            Đánh giá
                                        </button>
                                    @endif
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
     style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center;">
    <div id="order-review-modal-box"
         style="position:relative; max-width:640px; width:100%; margin:0 16px; background:#ffffff; border-radius:16px; box-shadow:0 20px 50px rgba(15,23,42,0.35); padding:20px 24px;">
        <button type="button"
                onclick="closeOrderReviewModal()"
                style="position:absolute; top:10px; right:14px; border:none; background:none; cursor:pointer; color:#6b7280;">
            ✕
        </button>
        <h3 style="font-size:18px; font-weight:600; margin-bottom:8px;">
            Đánh giá sản phẩm <span id="order-review-product-name" style="color:#b45309;"></span>
        </h3>
        <p style="font-size:13px; color:#6b7280; margin-bottom:16px;">
            Bạn chỉ có thể đánh giá khi đơn hàng đã được giao thành công.
        </p>

        <form id="order-review-form" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="rating-input" style="margin-bottom:12px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Số sao:</label>
                <div class="star-selector" id="order-review-stars" style="display:flex; gap:6px;">
                    @for($i=1;$i<=5;$i++)
                        <label style="cursor:pointer;">
                            <input type="radio" name="rating" value="{{ $i }}" {{ $i==5 ? 'checked' : '' }} style="display:none;">
                            <span data-value="{{ $i }}" style="font-size:20px; color:{{ $i <= 5 ? '#fbbf24' : '#d1d5db' }};">★</span>
                        </label>
                    @endfor
                </div>
            </div>
            <div class="form-group" style="margin-bottom:12px;">
                <label for="order-review-comment" style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Nhận xét của bạn</label>
                <textarea id="order-review-comment" name="comment" rows="4"
                          style="width:100%; border-radius:10px; border:1px solid #d1d5db; padding:8px 10px; font-size:13px;"
                          placeholder="Chất lượng sản phẩm, trải nghiệm sử dụng..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom:16px;">
                <label for="order-review-images" style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Ảnh kèm theo (tối đa 5 ảnh)</label>
                <input type="file" name="images[]" id="order-review-images" accept="image/*" multiple>
            </div>
            <button type="submit"
                    style="border:none; border-radius:999px; padding:10px 18px; background:linear-gradient(135deg,#b45309,#92400e); color:#fff; font-size:13px; font-weight:600; cursor:pointer;">
                Gửi đánh giá
            </button>
        </form>
    </div>
</div>

<script>
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

function openOrderReviewModal(productId, productName) {
    const modal = document.getElementById('order-review-modal');
    const box   = document.getElementById('order-review-modal-box');
    const form  = document.getElementById('order-review-form');
    const nameSpan = document.getElementById('order-review-product-name');
    const baseUrl  = modal.dataset.reviewBaseUrl;

    if (!modal || !box || !form) return;
    form.action = baseUrl + '/' + productId + '/reviews';
    if (nameSpan) nameSpan.textContent = productName || '';
    modal.style.display = 'flex';
}

function closeOrderReviewModal() {
    const modal = document.getElementById('order-review-modal');
    if (modal) modal.style.display = 'none';
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
