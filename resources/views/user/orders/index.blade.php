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
                                
                                @if($order->status === 'pending')
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

<script>
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        // Gửi request hủy đơn hàng
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
</script>
@endsection
