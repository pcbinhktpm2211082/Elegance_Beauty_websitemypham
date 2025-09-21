document.addEventListener('DOMContentLoaded', () => {
    // Chọn các phần tử cần thiết
    const avatarUpload = document.getElementById('avatar-upload');
    const changeAvatarBtn = document.getElementById('change-avatar-btn');
    const profileAvatar = document.getElementById('profile-avatar');
    const profileForm = document.getElementById('profile-form');
    const changePasswordForm = document.getElementById('change-password-form');
    const favoriteProducts = document.getElementById('favorite-products');

    // Xử lý thay đổi avatar
    if (changeAvatarBtn && avatarUpload) {
        changeAvatarBtn.addEventListener('click', () => {
            avatarUpload.click();
        });

        avatarUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    profileAvatar.src = event.target.result;
                    // Ở đây bạn có thể thêm logic upload ảnh lên server
                    showNotification('Ảnh đại diện đã được cập nhật');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Xử lý form thông tin cá nhân
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Validation
            const fullname = document.getElementById('fullname');
            const phone = document.getElementById('phone');
            const birthday = document.getElementById('birthday');
            const address = document.getElementById('address');

            let isValid = true;

            // Validate Họ và Tên
            if (!fullname.value.trim()) {
                showError(fullname, 'Vui lòng nhập Họ và Tên');
                isValid = false;
            }

            // Validate Số Điện Thoại
            const phoneRegex = /^(0[3|5|7|8|9])+([0-9]{8})$/;
            if (!phone.value.trim()) {
                showError(phone, 'Vui lòng nhập Số Điện Thoại');
                isValid = false;
            } else if (!phoneRegex.test(phone.value)) {
                showError(phone, 'Số Điện Thoại không hợp lệ');
                isValid = false;
            }

            if (isValid) {
                const profileData = {
                    fullname: fullname.value,
                    phone: phone.value,
                    birthday: birthday.value,
                    address: address.value
                };

                // Giả lập gửi dữ liệu
                console.log('Cập nhật thông tin:', profileData);
                showNotification('Cập nhật thông tin thành công');
            }
        });
    }

    // Xử lý form đổi mật khẩu
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current-password');
            const newPassword = document.getElementById('new-password');
            const confirmNewPassword = document.getElementById('confirm-new-password');

            let isValid = true;

            // Validate Current Password
            if (!currentPassword.value) {
                showError(currentPassword, 'Vui lòng nhập Mật Khẩu Hiện Tại');
                isValid = false;
            }

            // Validate New Password
            if (!newPassword.value) {
                showError(newPassword, 'Vui lòng nhập Mật Khẩu Mới');
                isValid = false;
            } else if (newPassword.value.length < 6) {
                showError(newPassword, 'Mật Khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }

            // Validate Confirm New Password
            if (!confirmNewPassword.value) {
                showError(confirmNewPassword, 'Vui lòng xác nhận Mật Khẩu Mới');
                isValid = false;
            } else if (newPassword.value !== confirmNewPassword.value) {
                showError(confirmNewPassword, 'Mật Khẩu Mới không khớp');
                isValid = false;
            }

            if (isValid) {
                // Giả lập đổi mật khẩu
                console.log('Đổi mật khẩu thành công');
                showNotification('Đổi mật khẩu thành công');
                changePasswordForm.reset();
            }
        });
    }

    // Xử lý sản phẩm yêu thích
    if (favoriteProducts) {
        favoriteProducts.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-favorite')) {
                const productCard = e.target.closest('.favorite-card');
                
                // Hiệu ứng xóa
                productCard.style.transition = 'all 0.5s ease';
                productCard.style.opacity = '0';
                productCard.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    productCard.remove();
                    showNotification('Đã xóa sản phẩm khỏi danh sách yêu thích');
                }, 500);
            }

            if (e.target.classList.contains('add-to-cart')) {
                const productName = e.target.closest('.favorite-card').querySelector('h4').textContent;
                showNotification(`Đã thêm ${productName} vào giỏ hàng`);
            }
        });
    }

    // Tab navigation
    const profileNav = document.querySelector('.profile-nav');
    const tabContents = document.querySelectorAll('.tab-content');

    if (profileNav) {
        profileNav.addEventListener('click', (e) => {
            const clickedTab = e.target.closest('li');
            if (!clickedTab) return;

            // Remove active class from all tabs and tab contents
            profileNav.querySelectorAll('li').forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            const tabId = clickedTab.getAttribute('data-tab');
            clickedTab.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    }

    // Hàm hiển thị thông báo
    function showNotification(message) {
        // Tạo phần tử thông báo
        const notification = document.createElement('div');
        notification.classList.add('notification');
        notification.textContent = message;
        
        // Thêm vào body
        document.body.appendChild(notification);
        
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 600);
        }, 3000);
    }

    // Hàm hiển thị lỗi
    function showError(input, message) {
        // Tìm hoặc tạo phần tử hiển thị lỗi
        let errorElement = input.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('div');
            errorElement.classList.add('error-message');
            input.parentNode.insertBefore(errorElement, input.nextSibling);
        }
        
        // Thêm lớp và nội dung lỗi
        input.classList.add('input-error');
        errorElement.textContent = message;
        errorElement.style.color = 'red';
        errorElement.style.fontSize = '0.8rem';
        errorElement.style.marginTop = '5px';
    }
}); 