<x-guest-layout>

    <!-- Session Status -->
    @if (session('status'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('oauth'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ $errors->first('oauth') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" />
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="checkbox-group">
            <input id="remember_me" type="checkbox" name="remember" />
            <label for="remember_me">Ghi nhớ đăng nhập</label>
        </div>

        <div class="form-actions">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Quên mật khẩu?
                </a>
            @endif
            <button type="submit" class="btn-primary">
                Đăng nhập
            </button>
        </div>
    </form>

    <div class="auth-divider">
        <span>Hoặc</span>
    </div>

    <a href="{{ route('google.redirect') }}" class="auth-social">
        <svg style="width: 20px; height: 20px;" viewBox="0 0 533.5 544.3" aria-hidden="true">
            <path fill="#4285f4" d="M533.5 278.4c0-18.6-1.5-37-4.7-54.8H272v103.7h146.9c-6.3 34.5-25.2 63.7-54 83.4v68h87.1c51 46.9 81 115.9 81 192.1z"/>
            <path fill="#34a853" d="M272 544.3c73.5 0 135.3-24.3 180.4-66.2l-87.1-68c-24.3 16.2-55.4 25.7-93.3 25.7-71.8 0-132.6-48.5-154.3-113.7H28.3v71.2C73.8 480.7 165.3 544.3 272 544.3z"/>
            <path fill="#fbbc04" d="M117.7 322.1c-10.3-30.5-10.3-63.4 0-93.9V157H28.3c-41.6 81.8-41.6 179.5 0 261.3z"/>
            <path fill="#ea4335" d="M272 107.7c38.8-.6 76.1 13.6 104.4 39.6l78-78C407 24.5 341.3.6 272 0 165.3 0 73.8 63.6 28.3 160.7l89.4 71.2C139.4 156.2 200.2 107.7 272 107.7z"/>
        </svg>
        Đăng nhập với Google
    </a>

    <div class="auth-footer">
        <span style="color: #666;">Chưa có tài khoản? </span>
        <a href="{{ route('register') }}">Đăng ký ngay</a>
    </div>
</x-guest-layout>
