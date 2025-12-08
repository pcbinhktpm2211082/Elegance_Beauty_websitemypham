<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" />
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('login') }}" class="forgot-link">
                Đã có tài khoản?
            </a>
            <button type="submit" class="btn-primary">
                Đăng ký
            </button>
        </div>
    </form>

    <div class="auth-footer">
        <span style="color: #666;">Đã có tài khoản? </span>
        <a href="{{ route('login') }}">Đăng nhập ngay</a>
    </div>
</x-guest-layout>
