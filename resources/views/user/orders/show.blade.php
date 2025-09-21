@extends('layouts.user')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>🛒 Đơn hàng {{ $order->order_code ?? ('#'.$order->id) }}</h1>
        <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }} • Trạng thái: {{ $order->status_text }}</p>
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
                <h2><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h2>
                <a href="{{ route('orders.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            <div class="order-details">
                <!-- Thông tin đơn hàng + giao hàng (gộp một khung) -->
                <div class="detail-section">
                    <div class="info-wrapper">
                        <div class="info-columns">
                            <div class="info-col">
                                <h3><i class="fas fa-shopping-bag"></i> Thông tin cơ bản</h3>
                                <div class="info-list">
                                    <div class="info-item"><label>Mã đơn hàng:</label><span>{{ $order->order_code ?? ('#'.$order->id) }}</span></div>
                                    <div class="info-item"><label>Ngày đặt:</label><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                                    <div class="info-item"><label>Trạng thái:</label><span class="status-badge status-{{ $order->status }}">{{ $order->status_text }}</span></div>
                                    <div class="info-item"><label>Thanh toán:</label><span>{{ $order->payment_method_text }}</span></div>
                                    <div class="info-item"><label>Tổng tiền:</label><span class="total-amount">{{ number_format($order->total_price, 0, ',', '.') }} đ</span></div>
                                </div>
                            </div>
                            <div class="info-col">
                                <h3><i class="fas fa-truck"></i> Thông tin giao hàng</h3>
                                <div class="info-list">
                                    <div class="info-item"><label>Người nhận:</label><span>{{ $order->customer_name ?? $order->user->name }}</span></div>
                                    <div class="info-item"><label>SĐT:</label><span>{{ $order->customer_phone ?? $order->user->phone ?? 'Chưa cập nhật' }}</span></div>
                                    <div class="info-item full"><label>Địa chỉ:</label><span>{{ $order->customer_address ?? $order->user->full_address ?? 'Chưa cập nhật' }}</span></div>
                                    <div class="info-item"><label>Đơn vị VC:</label><span>{{ $order->shipping_carrier ?? 'Đang cập nhật' }}</span></div>
                                    <div class="info-item"><label>Mã vận đơn:</label><span>{{ $order->tracking_code ?? 'Đang cập nhật' }}</span></div>
                                    <div class="info-item"><label>Dự kiến giao:</label><span>{{ !empty($order->estimated_delivery_at) ? (is_object($order->estimated_delivery_at) ? $order->estimated_delivery_at->format('d/m/Y') : $order->estimated_delivery_at) : 'Đang cập nhật' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm + Ghi chú + Tổng kết (gộp chung) -->
                <div class="detail-section">
                    <h3><i class="fas fa-box"></i> 📦 Danh sách sản phẩm</h3>
                    <div class="order-table-wrapper">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>SL</th>
                                    <th>Giá</th>
                                    <th>Tạm tính</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="col-image">
                                            @php $img = $item->product && $item->product->coverOrFirstImage ? asset('storage/'.$item->product->coverOrFirstImage) : asset('storage/placeholder.jpg'); @endphp
                                            <img src="{{ $img }}" alt="{{ $item->product->name ?? $item->product_name }}">
                                        </td>
                                        <td class="col-name">
                                            <div class="name">{{ $item->product->name ?? $item->product_name }}</div>
                                            @if(!empty($item->variant_name))
                                                <div class="variant">{{ $item->variant_name }}</div>
                                            @endif
                                        </td>
                                        <td class="col-qty">{{ $item->quantity }}</td>
                                        <td class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                        <td class="col-subtotal">{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h3 style="margin-top:14px"><i class="fas fa-sticky-note"></i> 📝 Ghi chú đơn hàng</h3>
                    <div class="note-box">{{ $order->note ?? 'Không có ghi chú' }}</div>

                    <div class="totals-box" style="margin-top:14px">
                        <div class="row"><span>Tạm tính:</span><span>{{ number_format($order->subtotal, 0, ',', '.') }} đ</span></div>
                        <div class="row"><span>Phí vận chuyển:</span><span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span></div>
                        <div class="divider"></div>
                        <div class="row grand"><span>Thành tiền:</span><strong>{{ number_format($order->total_price, 0, ',', '.') }} đ</strong></div>
                    </div>
                </div>

                <!-- Lịch sử trạng thái -->
                @if($order->status_history && count($order->status_history) > 0)
                <div class="detail-section">
                    <h3><i class="fas fa-history"></i> Lịch sử trạng thái</h3>
                    <div class="status-timeline">
                        @foreach($order->status_history as $status)
                            <div class="timeline-item">
                                <div class="timeline-icon">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h4>{{ $status->status_text }}</h4>
                                    <p>{{ $status->created_at->format('d/m/Y H:i:s') }}</p>
                                    @if($status->note)
                                        <p class="status-note">{{ $status->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Hành động -->
                <div class="order-actions">
                    @if($order->status === 'pending')
                        <button class="cancel-order-btn" onclick="cancelOrder({{ $order->id }})">
                            <i class="fas fa-times"></i>
                            Hủy đơn hàng
                        </button>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" class="back-to-orders-btn">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
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
</script>
@endsection
