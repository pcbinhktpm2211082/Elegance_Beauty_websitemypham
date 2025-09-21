@extends('layouts.user')

@section('title', 'Liên hệ')

@section('content')
<main class="contact-main">
    <div class="contact-header">
        <h1 class="contact-title">Liên hệ với chúng tôi</h1>
        <p class="contact-subtitle">Chúng tôi luôn sẵn sàng hỗ trợ bạn mọi lúc</p>
    </div>

    <div class="contact-container">
        <!-- Thông tin liên hệ -->
        <div class="contact-info">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Địa chỉ</h3>
                    <p>---------------------</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <h3>Điện thoại</h3>
                    <p>+84 ---------------------</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>info@elegancebeauty.com</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <h3>Giờ làm việc</h3>
                    <p>Thứ 2 - Thứ 7: 8:00 - 20:00<br>Chủ nhật: 9:00 - 18:00</p>
                </div>
            </div>
        </div>

        <!-- Form liên hệ -->
        <div class="contact-form-container">
            <div class="form-header">
                <h2>Gửi tin nhắn cho chúng tôi</h2>
                <p>Hãy để lại thông tin và tin nhắn của bạn, chúng tôi sẽ phản hồi sớm nhất có thể</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @auth
            <form class="contact-form" method="POST" action="{{ route('contact.store') }}">
                @csrf
                
                <!-- Debug CSRF Token -->
                @if(config('app.debug'))
                    <input type="hidden" name="_debug" value="1">
                @endif
                
                <div class="form-group">
                    <label for="name">Họ và tên *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           class="form-input @error('name') error @enderror"
                           placeholder="Nhập họ và tên của bạn">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           class="form-input @error('email') error @enderror"
                           placeholder="Nhập email của bạn">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title">Tiêu đề *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required 
                           class="form-input @error('title') error @enderror"
                           placeholder="Nhập tiêu đề tin nhắn">
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Nội dung tin nhắn *</label>
                    <textarea id="content" 
                              name="content" 
                              required 
                              rows="6"
                              class="form-textarea @error('content') error @enderror"
                              placeholder="Nhập nội dung tin nhắn của bạn (tối thiểu 10 ký tự)">{{ old('content') }}</textarea>
                    @error('content')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Gửi tin nhắn
                    </button>
                </div>
            </form>
            @else
            <div class="alert alert-error" style="margin-top:16px">
                <i class="fas fa-exclamation-circle"></i>
                Bạn cần đăng nhập để gửi liên hệ.
                <a href="{{ route('login') }}" class="cta-button" style="margin-left:8px">Đăng nhập</a>
                <a href="{{ route('register') }}" class="cta-button secondary" style="margin-left:8px">Đăng ký</a>
            </div>
            @endauth
        </div>
    </div>
</main>
@endsection
