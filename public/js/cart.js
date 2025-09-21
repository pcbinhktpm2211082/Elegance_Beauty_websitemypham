document.addEventListener('DOMContentLoaded', () => {
    const cartItems = document.getElementById('cart-items');
    const cartSummary = document.querySelector('.cart-summary');
    const continueShoppingBtn = document.querySelector('.continue-shopping');
    const checkoutBtn = document.querySelector('.checkout');

    // Xử lý tăng/giảm số lượng sản phẩm
    cartItems.addEventListener('click', (e) => {
        const target = e.target;
        const quantityInput = target.parentElement.querySelector('input');
        
        if (target.classList.contains('qty-btn')) {
            let currentValue = parseInt(quantityInput.value);
            
            if (target.classList.contains('plus')) {
                quantityInput.value = currentValue + 1;
            } else if (target.classList.contains('minus') && currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
            
            updateCartSummary();
        }

        // Xóa sản phẩm
        if (target.classList.contains('remove-item')) {
            target.closest('.cart-item').remove();
            updateCartSummary();
        }
    });

    // Cập nhật tổng giá và số lượng
    function updateCartSummary() {
        const items = document.querySelectorAll('.cart-item');
        let totalItems = 0;
        let totalPrice = 0;
        const shippingFee = 30000;

        items.forEach(item => {
            const quantity = parseInt(item.querySelector('input').value);
            const price = parseFloat(item.querySelector('p').textContent.replace(/[^0-9.-]+/g,""));
            
            totalItems += quantity;
            totalPrice += price * quantity;
        });

        const summaryRows = cartSummary.querySelectorAll('.summary-row');
        summaryRows[0].querySelector('span:last-child').textContent = totalItems;
        summaryRows[1].querySelector('span:last-child').textContent = totalPrice.toLocaleString() + ' VNĐ';
        summaryRows[2].querySelector('span:last-child').textContent = shippingFee.toLocaleString() + ' VNĐ';
        summaryRows[3].querySelector('span:last-child').textContent = (totalPrice + shippingFee).toLocaleString() + ' VNĐ';
    }

    // Nút tiếp tục mua hàng
    if (continueShoppingBtn) {
        continueShoppingBtn.addEventListener('click', () => {
            window.location.href = 'products.html';
        });
    }

    // Nút thanh toán
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            alert('Chức năng thanh toán đang được phát triển');
            // Trong thực tế, bạn sẽ chuyển đến trang thanh toán
        });
    }

    // Khởi tạo ban đầu
    updateCartSummary();
}); 