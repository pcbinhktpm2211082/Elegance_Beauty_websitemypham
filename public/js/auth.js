// Xử lý đăng ký
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');

    // Validation cho form đăng ký
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Lấy các giá trị từ form
            const fullname = document.getElementById('fullname');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm-password');
            const terms = document.getElementById('terms');

            // Reset các thông báo lỗi cũ
            clearErrors(registerForm);

            // Validation
            let isValid = true;

            // Validate Họ và Tên
            if (!fullname.value.trim()) {
                showError(fullname, 'Vui lòng nhập Họ và Tên');
                isValid = false;
            }

            // Validate Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'Vui lòng nhập Email');
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                showError(email, 'Địa chỉ Email không hợp lệ');
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

            // Validate Mật Khẩu
            if (!password.value) {
                showError(password, 'Vui lòng nhập Mật Khẩu');
                isValid = false;
            } else if (password.value.length < 6) {
                showError(password, 'Mật Khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }

            // Validate Xác Nhận Mật Khẩu
            if (!confirmPassword.value) {
                showError(confirmPassword, 'Vui lòng xác nhận Mật Khẩu');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Mật Khẩu không khớp');
                isValid = false;
            }

            // Validate Điều Khoản
            if (!terms.checked) {
                showError(terms, 'Vui lòng đồng ý với Điều Khoản');
                isValid = false;
            }

            // Nếu form hợp lệ
            if (isValid) {
                const userData = {
                    fullname: fullname.value,
                    email: email.value,
                    phone: phone.value,
                    password: password.value
                };

                console.log('Đăng ký thành công:', userData);
                alert('Đăng ký thành công!');
                window.location.href = 'login.html';
            }
        });
    }

    // Validation cho form đăng nhập
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Lấy các giá trị từ form
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            // Reset các thông báo lỗi cũ
            clearErrors(loginForm);

            // Validation
            let isValid = true;

            // Validate Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'Vui lòng nhập Email');
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                showError(email, 'Địa chỉ Email không hợp lệ');
                isValid = false;
            }

            // Validate Mật Khẩu
            if (!password.value) {
                showError(password, 'Vui lòng nhập Mật Khẩu');
                isValid = false;
            }

            // Nếu form hợp lệ
            if (isValid) {
                const loginData = {
                    email: email.value,
                    password: password.value
                };

                console.log('Đăng nhập:', loginData);
                alert('Đăng nhập thành công!');
                window.location.href = 'index.html';
            }
        });
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

    // Hàm xóa các thông báo lỗi
    function clearErrors(form) {
        const errorMessages = form.querySelectorAll('.error-message');
        const inputErrors = form.querySelectorAll('.input-error');
        
        errorMessages.forEach(el => el.remove());
        inputErrors.forEach(el => el.classList.remove('input-error'));
    }
}); 