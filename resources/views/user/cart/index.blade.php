@extends('layouts.user')

@section('title', 'Giỏ hàng')

@section('content')
<main>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Giỏ hàng của bạn</h1>
            @if($cartCount > 0)
                <p>Có {{ $cartCount }} sản phẩm trong giỏ hàng</p>
            @else
                <p>Giỏ hàng trống</p>
            @endif
        </div>

        @if(count($cartItems) > 0)
            <div class="cart-content">
                <div class="cart-items">
                    @foreach($cartItems as $productId => $item)
                        <div class="cart-item" data-id="{{ $productId }}">
                            <div class="item-image">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                                @else
                                    <img src="{{ asset('storage/placeholder.jpg') }}" alt="Không có ảnh">
                                @endif
                            </div>
                            
                            <div class="item-details">
                                <h3>{{ $item['name'] }}</h3>
                                <p class="item-price">{{ number_format($item['price'], 0, ',', '.') }} VNĐ</p>
                            </div>
                            
                            <div class="item-quantity">
                                <label>Số lượng:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" onclick="updateQuantity('{{ $productId }}', {{ $item['variant_id'] ?? 'null' }}, -1)">-</button>
                                    <input type="number" value="{{ $item['quantity'] }}" min="1" max="99" 
                                           onchange="updateQuantity('{{ $productId }}', {{ $item['variant_id'] ?? 'null' }}, 0, this.value)">
                                    <button class="qty-btn" onclick="updateQuantity('{{ $productId }}', {{ $item['variant_id'] ?? 'null' }}, 1)">+</button>
                                </div>
                            </div>
                            
                            <div class="item-subtotal">
                                <span class="subtotal-amount">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VNĐ</span>
                            </div>
                            
                            <div class="item-actions">
                                <button class="remove-btn" onclick="removeFromCart('{{ $productId }}', {{ $item['variant_id'] ?? 'null' }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="cart-summary">
                    <div class="summary-header">
                        <h2>Tổng đơn hàng</h2>
                    </div>
                    
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Tổng cộng:</span>
                            <span class="cart-total">{{ number_format($cartTotal, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                    
                    <div class="summary-actions">
                        <button class="clear-cart-btn" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Xóa tất cả
                        </button>
                        <a href="{{ route('products.index') }}" class="continue-shopping-btn">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <button class="checkout-btn" onclick="checkout()">
                            <i class="fas fa-shopping-cart"></i> Thanh toán
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Giỏ hàng trống</h2>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="{{ route('products.index') }}" class="start-shopping-btn">
                    <i class="fas fa-shopping-bag"></i> Bắt đầu mua sắm
                </a>
            </div>
        @endif
    </div>
</main>

<script>
function updateQuantity(itemId, variantId, change, newValue = null) {
    let quantity;
    if (newValue !== null) {
        quantity = parseInt(newValue);
    } else {
        const input = document.querySelector(`[data-id="${itemId}"] input[type="number"]`);
        quantity = parseInt(input.value) + change;
    }
    
    if (quantity < 1) quantity = 1;
    if (quantity > 99) quantity = 99;
    
    // Tách productId từ itemId (itemId có thể là "4_6" hoặc "4")
    const productId = itemId.split('_')[0];
    
    // Sử dụng JSON thay vì FormData
    const data = {
        quantity: quantity,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    // Thêm variant_id nếu có
    if (variantId && variantId !== 'null') {
        data.variant_id = variantId;
    }
    
    console.log('updateQuantity called:', { itemId, productId, variantId, quantity });
    
    fetch(`/cart/update/${productId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(() => { throw new Error('Server returned non-JSON response'); });
        }
    })
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng hiển thị
            document.querySelector(`[data-id="${itemId}"] input[type="number"]`).value = quantity;
            document.querySelector(`[data-id="${itemId}"] .subtotal-amount`).textContent = data.item_subtotal + ' VNĐ';
            document.querySelector('.cart-total').textContent = data.cart_total + ' VNĐ';
            
            // Cập nhật số lượng trong header
            updateCartCount(data.cart_count);
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => { showNotification('Có lỗi xảy ra: ' + error.message, 'error'); });
}

function removeFromCart(itemId, variantId) {
    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        return;
    }
    
    // Tách productId từ itemId (itemId có thể là "4_6" hoặc "4")
    const productId = itemId.split('_')[0];
    
    // Sử dụng JSON thay vì FormData
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    // Thêm variant_id nếu có
    if (variantId && variantId !== 'null') {
        data.variant_id = variantId;
    }
    
    console.log('removeFromCart called:', { itemId, productId, variantId });
    
    fetch(`/cart/remove/${productId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(() => { throw new Error('Server returned non-JSON response'); });
        }
    })
    .then(data => {
        if (data.success) {
            // Xóa item khỏi DOM
            document.querySelector(`[data-id="${itemId}"]`).remove();
            
            // Cập nhật tổng tiền
            document.querySelector('.cart-total').textContent = data.cart_total + ' VNĐ';
            
            // Cập nhật số lượng trong header
            updateCartCount(data.cart_count);
            
            // Kiểm tra nếu giỏ hàng trống
            const cartItems = document.querySelectorAll('.cart-item');
            if (cartItems.length === 0) {
                location.reload();
            }
            
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => { showNotification('Có lỗi xảy ra: ' + error.message, 'error'); });
}

function clearCart() {
    if (!confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
        return;
    }
    
    
    
    // Sử dụng JSON thay vì FormData
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    
    
    fetch('/cart/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(() => { throw new Error('Server returned non-JSON response'); });
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => { showNotification('Có lỗi xảy ra: ' + error.message, 'error'); });
}

function checkout() {
    window.location.href = '{{ route("payment.checkout") }}';
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        if (count === 0) {
            cartCountElement.style.display = 'none';
        } else {
            cartCountElement.style.display = 'block';
        }
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Các hàm debug nội bộ đã được loại bỏ khỏi bản production
</script>
@endsection
