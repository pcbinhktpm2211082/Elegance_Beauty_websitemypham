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
