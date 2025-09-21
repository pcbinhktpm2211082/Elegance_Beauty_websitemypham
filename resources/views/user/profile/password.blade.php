@extends('layouts.user')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>Đổi mật khẩu</h1>
        <p>Bảo mật tài khoản của bạn</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-container">
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-key"></i> Thay đổi mật khẩu</h2>
                <a href="{{ route('profile.show') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            <form method="POST" action="{{ route('profile.update-password') }}" class="password-form">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại *</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required 
                               class="form-input @error('current_password') error @enderror"
                               placeholder="Nhập mật khẩu hiện tại">
                        @error('current_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới *</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               required 
                               class="form-input @error('new_password') error @enderror"
                               placeholder="Mật khẩu mới (tối thiểu 8 ký tự)">
                        @error('new_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Xác nhận mật khẩu mới *</label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required 
                               class="form-input @error('new_password_confirmation') error @enderror"
                               placeholder="Nhập lại mật khẩu mới">
                        @error('new_password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="password-hint">
                    <h4><i class="fas fa-info-circle"></i> Lưu ý về mật khẩu:</h4>
                    <ul>
                        <li>Mật khẩu phải có ít nhất 8 ký tự</li>
                        <li>Nên sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                        <li>Không nên sử dụng thông tin cá nhân làm mật khẩu</li>
                        <li>Thay đổi mật khẩu thường xuyên để bảo mật</li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-key"></i>
                        Đổi mật khẩu
                    </button>
                    
                    <a href="{{ route('profile.show') }}" class="cancel-btn">
                        <i class="fas fa-times"></i>
                        Hủy bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
