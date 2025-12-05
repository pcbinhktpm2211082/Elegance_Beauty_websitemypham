@extends('layouts.user')

@section('title', 'Thanh toán đơn hàng')

@section('content')
<div class="checkout-page">
    <div class="checkout-header">
        <h1>Thanh toán đơn hàng</h1>
        <p>Hoàn tất thông tin giao hàng và chọn phương thức thanh toán</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="checkout-container">
        <div class="checkout-content">
            <!-- Form thông tin giao hàng -->
            <div class="checkout-section">
                <div class="section-header">
                    <h2><i class="fas fa-map-marker-alt"></i> Thông tin giao hàng</h2>
                </div>
                
                <form method="POST" action="{{ route('payment.process') }}" class="checkout-form" id="checkout_form">
                    @csrf
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="shipping_name">Họ và tên người nhận *</label>
                            <input type="text" 
                                   id="shipping_name" 
                                   name="shipping_name" 
                                   value="{{ old('shipping_name', $user->name) }}" 
                                   required 
                                   class="form-input @error('shipping_name') error @enderror"
                                   placeholder="Nhập họ và tên người nhận">
                            @error('shipping_name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="shipping_phone">Số điện thoại *</label>
                            <input type="tel" 
                                   id="shipping_phone" 
                                   name="shipping_phone" 
                                   value="{{ old('shipping_phone', $user->phone) }}" 
                                   required 
                                   class="form-input @error('shipping_phone') error @enderror"
                                   placeholder="Số điện thoại liên hệ">
                            @error('shipping_phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address">Địa chỉ giao hàng *</label>
                        <textarea id="shipping_address" 
                                  name="shipping_address" 
                                  rows="3"
                                  required
                                  class="form-textarea @error('shipping_address') error @enderror"
                                  placeholder="Nhập địa chỉ chi tiết giao hàng">{{ old('shipping_address', $user->full_address) }}</textarea>
                        @error('shipping_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shipping_note">Ghi chú giao hàng</label>
                        <textarea id="shipping_note" 
                                  name="shipping_note" 
                                  rows="2"
                                  class="form-textarea @error('shipping_note') error @enderror"
                                  placeholder="Ghi chú về đơn hàng (tùy chọn)">{{ old('shipping_note') }}</textarea>
                        @error('shipping_note')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="payment-methods">
                        <h3><i class="fas fa-credit-card"></i> Phương thức thanh toán</h3>
                        
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" 
                                       id="cash_on_delivery" 
                                       name="payment_method" 
                                       value="cash_on_delivery" 
                                       {{ old('payment_method') == 'cash_on_delivery' ? 'checked' : '' }}
                                       required>
                                <label for="cash_on_delivery" class="payment-label">
                                    <div class="payment-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h4>Thanh toán khi nhận hàng (COD)</h4>
                                        <p>Thanh toán bằng tiền mặt khi nhận hàng</p>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" 
                                       id="bank_transfer" 
                                       name="payment_method" 
                                       value="bank_transfer" 
                                       {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}
                                       required>
                                <label for="bank_transfer" class="payment-label">
                                    <div class="payment-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h4>Chuyển khoản ngân hàng</h4>
                                        <p>Chuyển khoản qua tài khoản ngân hàng</p>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" 
                                       id="online_payment" 
                                       name="payment_method" 
                                       value="online_payment" 
                                       {{ old('payment_method') == 'online_payment' ? 'checked' : '' }}
                                       required>
                                <label for="online_payment" class="payment-label">
                                    <div class="payment-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h4>Thanh toán trực tuyến</h4>
                                        <p>Thanh toán qua thẻ tín dụng/ghi nợ</p>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" 
                                       id="stripe" 
                                       name="payment_method" 
                                       value="stripe" 
                                       {{ old('payment_method') == 'stripe' ? 'checked' : '' }}
                                       required>
                                <label for="stripe" class="payment-label">
                                    <div class="payment-icon" style="background: white; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; justify-content: center; min-width: 120px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                        <svg width="100" height="40" viewBox="0 0 468 222" xmlns="http://www.w3.org/2000/svg" style="max-width: 100px; height: auto; display: block;">
                                            <path d="M414 113.4c0-25.6-12.4-45.8-36.1-45.8-23.8 0-38.2 20.2-38.2 45.6 0 30.1 17 45.3 41.4 45.3 11.9 0 20.9-2.7 27.7-6.5V132c-6.8 3.4-14.6 5.5-24.5 5.5-9.7 0-18.3-3.4-19.4-15.2h48.9c0-1.3.2-6.5.2-6.9zm-49.4-9.5c0-11.3 6.9-16 13.2-16 6.1 0 12.6 4.7 12.6 16h-25.8zm-40.8 22.5c-4.8 0-8-1.9-10.4-4.3l-3.7 18.2h-12.1l6-29.4c-2.3-1.2-9.4-2-14.3-2-14.6 0-24.5 7.8-24.5 19.3 0 15.5 12.8 19.2 22.6 22.9 10.3 3.9 12.4 6.1 12.4 9.4 0 5.5-4.1 8.1-9.6 8.1-8.1 0-12.7-2.3-17.1-5l-3.8 18.6c4.3 2 10.6 3.7 17.7 3.7 15.4 0 25.1-7.6 25.1-19.6 0-12.5-8.8-18.1-19.4-22.1-8.3-3.2-13.4-5.3-13.4-9.2 0-3.3 2.6-6.5 7.8-6.5zm-42.1 0c-5 0-8.5 1.9-10.9 4.3l-8-37.3h-13.1l7.3 36.3c-3.6 2.3-8.3 3.7-13.1 3.7h-.5l-2.1 10.1h8.1c3.3 0 5.9-.4 7.1-1.1l2.1-10.1c.8.1 1.6.1 2.4.1 5.8 0 10.1-2.1 12.5-5.3l-7.5-37.2h13.1l8 37.3c2.4-2.3 5.9-4.2 10.9-4.2zm-52.2 0c-9.1 0-16.8 5.1-19.4 13.1l-1.3-5.9h-12.8c2.1 10.2 7.8 19.3 18.1 19.3 2.2 0 4.7-.3 6.3-.8l2.1-10c-1.4.4-3.1.7-5.2.7-6.5 0-10.5-3.7-12.2-9.8l29.1-7.2c.3-1.1.4-2.3.4-3.4 0-8.2-5.1-13.9-13.1-13.9zm-6.1 7.8l-10.7 2.7c2.1-5.3 5.9-7.5 9.5-7.5 5.1 0 7.8 3.3 7.8 7.8 0 2.6-.8 4.4-2.1 5.8-.6.7-1.4 1.2-2.3 1.5zm-40.5-7.8c-4.2 0-7.5 1.8-9.5 4.1l-3.6-3.4c2.8-3.2 7.5-5.1 13.3-5.1 8.9 0 15.2 4.8 15.2 12.4v.6h-20.9c1.1 5.9 5.2 9.1 10.9 9.1 4.1 0 7.3-1.3 9.5-3.1l3.6 3.8c-3.1 3.5-8.3 5.3-14.1 5.3-12.1 0-19.4-7.1-19.4-17.3 0-10.1 7.3-17.2 19.4-17.2 11.8 0 18.1 7.1 18.1 17.2v2.1h-12.6zm-25.1 0c-9.1 0-16.1 4.9-18.7 12.8l-1.3-5.6h-12.4c2.8 10.1 9.4 19.2 20.1 19.2 2.1 0 4.5-.3 6.2-.7l2 9.7c-1.6.4-3.8.7-6.2.7-11.8 0-20.1-7.8-22.8-19.3l-.1-.5h29.1c.1.9.2 1.8.2 2.6 0 7.7-3.3 12.8-9.1 12.8-5.6 0-9.5-3.6-11.2-9.3l-10.6 2.6c2.6 7.8 8.9 12.9 17.3 12.9 10.9 0 18.1-7.1 18.1-17.2 0-9.9-6.8-17.1-17.7-17.1zm-40.2 0c-5.3 0-9.1 2-11.5 4.8l-3.4-3.1c3.3-4 8.9-6.1 15.2-6.1 12.1 0 19.5 7.1 19.5 17.2v1.3h-28.4c1.1 6.2 5.9 9.7 12.2 9.7 4.4 0 7.8-1.5 10.1-3.8l3.4 3.6c-3.2 3.9-8.7 5.9-15.1 5.9-12.4 0-20.5-7.3-20.5-17.3 0-10 8.1-17.2 20.5-17.2 11.9 0 19.2 7.2 19.2 17.2v2.1h-12.2zm-40.9-9.4l-5.1 26.1h12.9l5-26.1h-12.8zm-1.1-8.9c-4.1 0-7.4 2.3-8.9 5.8l-1-5h-11.1l-6.1 30.7h12.9l.9-4.4c1.4 1 3.6 1.6 5.8 1.6 7.3 0 12.1-4.5 13.8-11.1l2.2-11.2c.3-1.4.4-2.7.4-3.9 0-2.1-.3-3.7-.9-4.5-.6-.8-1.8-1.2-3.5-1.2z" fill="#635BFF"/>
                                        </svg>
                                    </div>
                                    <div class="payment-info">
                                        <h4>Thanh toán bằng Thẻ Quốc tế</h4>
                                        <p>Thanh toán qua thẻ Visa, Mastercard, American Express</p>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" 
                                       id="vnpay" 
                                       name="payment_method" 
                                       value="vnpay" 
                                       {{ old('payment_method') == 'vnpay' ? 'checked' : '' }}
                                       required>
                                <label for="vnpay" class="payment-label">
                                    <div class="payment-icon" style="background: white; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; justify-content: center; min-width: 120px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                        <img src="https://sandbox.vnpayment.vn/paymentv2/images/logo.png" alt="VNPAY" style="height: 40px; max-width: 140px; object-fit: contain; display: block;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display: none; font-weight: bold; color: #00B14F; font-size: 22px; letter-spacing: 1px;">VNPAY</div>
                                    </div>
                                    <div class="payment-info">
                                        <h4>VNPAY-QR</h4>
                                        <p>Thanh toán qua QR Code VNPAY</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        @error('payment_method')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="checkout-actions">
                        <button type="submit" class="checkout-btn">
                            <i class="fas fa-lock"></i>
                            Xác nhận đặt hàng
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="back-to-cart-btn">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại giỏ hàng
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar thông tin đơn hàng -->
        <div class="checkout-sidebar">
            <div class="order-summary-card">
                <h3><i class="fas fa-shopping-cart"></i> Tóm tắt đơn hàng</h3>
                
                <div class="order-items">
                    @foreach($cartItems as $productId => $item)
                        <div class="order-item">
                            <div class="item-image">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" 
                                         alt="{{ $item['name'] }}">
                                    <!-- Debug: {{ $item['image'] }} -->
                                @else
                                    <img src="{{ asset('storage/placeholder.jpg') }}" 
                                         alt="Không có ảnh">
                                    <!-- Debug: No image -->
                                @endif
                            </div>
                            <div class="item-info">
                                <h4>{{ $item['name'] }}</h4>
                                <p class="item-price">{{ number_format($item['price'], 0, ',', '.') }} VNĐ</p>
                                <p class="item-quantity">Số lượng: {{ $item['quantity'] }}</p>
                            </div>
                            <div class="item-total">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="order-totals">
                    <div class="voucher-box" style="margin-bottom:12px;">
                        <label for="voucher_code" style="display:block; font-weight:600; margin-bottom:6px;">Mã giảm giá</label>
                        <div style="display:flex; gap:8px;">
                            <input form="checkout_form" id="voucher_input_proxy" type="text" placeholder="Nhập mã voucher" class="form-input" style="flex:1;">
                            <button type="button" class="btn-secondary" onclick="applyVoucherFromSidebar()">Áp dụng</button>
                        </div>
                        <!-- hidden real input inside form -->
                        <input type="hidden" name="voucher_code" id="voucher_code_hidden" form="checkout_form" />
                    </div>
                    <div class="total-row">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($subtotal, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="total-row">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="total-row" id="discount_row" style="display:none; color:#16a34a;">
                        <span>Giảm giá:</span>
                        <strong id="discount_amount">-0 VNĐ</strong>
                    </div>
                    <div class="total-row total">
                        <span>Tổng cộng:</span>
                        <strong id="total_amount_display">{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</strong>
                    </div>
                </div>

                <div class="shipping-info">
                    <h4><i class="fas fa-truck"></i> Thông tin vận chuyển</h4>
                    <ul>
                        <li>Giao hàng trong vòng 2-5 ngày làm việc</li>
                        <li>Phí vận chuyển: 30,000 VNĐ</li>
                        <li>Miễn phí vận chuyển cho đơn hàng từ 500,000 VNĐ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function numberFormat(n){
    try{ return new Intl.NumberFormat('vi-VN').format(n); }catch(e){ return n; }
}

async function applyVoucherFromSidebar(){
    const proxy = document.getElementById('voucher_input_proxy');
    const hidden = document.getElementById('voucher_code_hidden');
    const discountRow = document.getElementById('discount_row');
    const discountAmountEl = document.getElementById('discount_amount');
    const totalDisplayEl = document.getElementById('total_amount_display');
    const form = document.getElementById('checkout_form');
    if(!proxy || !hidden || !form) return;

    const code = proxy.value.trim();
    hidden.value = code; // đồng bộ vào input ẩn để lưu cùng order khi submit

    if(code === ''){
        if(discountRow){ discountRow.style.display = 'none'; }
        if(discountAmountEl){ discountAmountEl.textContent = '-0 VNĐ'; }
        return;
    }

    // Lấy CSRF token từ form
    const csrf = form.querySelector('input[name="_token"]').value;
    try{
        const res = await fetch("{{ route('api.voucher.preview') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ voucher_code: code })
        });
        const data = await res.json();
        if(data && data.ok){
            const discount = data.data.discount || 0;
            const total = data.data.total || 0;
            if(discountRow){ discountRow.style.display = discount > 0 ? 'flex' : 'none'; }
            if(discountAmountEl){ discountAmountEl.textContent = '-' + numberFormat(discount) + ' VNĐ'; }
            if(totalDisplayEl){ totalDisplayEl.textContent = numberFormat(total) + ' VNĐ'; }
        } else {
            // Không áp dụng được voucher: ẩn hàng giảm giá, giữ tổng cũ
            if(discountRow){ discountRow.style.display = 'none'; }
            if(discountAmountEl){ discountAmountEl.textContent = '-0 VNĐ'; }
            // Optional: hiển thị thông báo
            if(data && data.message){ alert(data.message); }
        }
    } catch(e){
        if(discountRow){ discountRow.style.display = 'none'; }
        if(discountAmountEl){ discountAmountEl.textContent = '-0 VNĐ'; }
        // Optional: log
        console.error(e);
    }
}
</script>
@endsection
