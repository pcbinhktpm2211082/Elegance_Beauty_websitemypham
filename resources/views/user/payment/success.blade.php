@extends('layouts.user')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="success-page">
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <div class="success-content">
            <h1>Đặt hàng thành công!</h1>
            <p>Cảm ơn bạn đã mua sắm tại shop của chúng tôi</p>
            
            <div class="order-info">
                <h3>Thông tin đơn hàng</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Mã đơn hàng:</label>
                        <span>#{{ $order->id }}</span>
                    </div>
                    <div class="info-item">
                        <label>Mã giao dịch:</label>
                        <span>{{ $order->order_code }}</span>
                    </div>
                    <div class="info-item">
                        <label>Ngày đặt hàng:</label>
                        <span>{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Tổng tiền:</label>
                        <span class="total-amount">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="info-item">
                        <label>Phương thức thanh toán:</label>
                        <span>
                            @switch($order->payment_method)
                                @case('cash_on_delivery')
                                    Thanh toán khi nhận hàng (COD)
                                    @break
                                @case('bank_transfer')
                                    Chuyển khoản ngân hàng
                                    @break
                                @case('online_payment')
                                    Thanh toán trực tuyến
                                    @break
                                @default
                                    {{ ucfirst($order->payment_method) }}
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>

            <div class="shipping-info">
                <h3>Thông tin giao hàng</h3>
                <div class="shipping-details">
                    <p><strong>Người nhận:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
                    @if($order->note)
                        <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
                    @endif
                </div>
            </div>

            <div class="next-steps">
                <h3>Những bước tiếp theo</h3>
                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Xác nhận đơn hàng</h4>
                            <p>Chúng tôi sẽ xác nhận đơn hàng trong vòng 24 giờ</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Chuẩn bị và giao hàng</h4>
                            <p>Đơn hàng sẽ được chuẩn bị và giao trong 2-5 ngày làm việc</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Nhận hàng</h4>
                            <p>Kiểm tra và thanh toán khi nhận hàng (nếu chọn COD)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('orders.show', $order) }}" class="view-order-btn">
                    <i class="fas fa-eye"></i>
                    Xem chi tiết đơn hàng
                </a>
                
                <a href="{{ route('products.index') }}" class="continue-shopping-btn">
                    <i class="fas fa-shopping-cart"></i>
                    Tiếp tục mua sắm
                </a>
            </div>

            <div class="contact-info">
                <h3>Liên hệ hỗ trợ</h3>
                <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi:</p>
                <div class="contact-details">
                    <p><i class="fas fa-phone"></i> Hotline: 1900-xxxx</p>
                    <p><i class="fas fa-envelope"></i> Email: support@shop.com</p>
                    <p><i class="fas fa-clock"></i> Giờ làm việc: 8:00 - 20:00 (Thứ 2 - Chủ nhật)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
