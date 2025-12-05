@extends('layouts.user')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>Thông tin cá nhân</h1>
        <p>Quản lý thông tin tài khoản của bạn</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-container">
        <!-- Thông tin cơ bản -->
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-user"></i> Thông tin cơ bản</h2>
                <a href="{{ route('profile.edit') }}" class="edit-btn">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
            </div>
            
            <div class="profile-info-grid">
                <div class="info-item">
                    <label>Họ và tên:</label>
                    <span>{{ $user->name ?? 'Chưa cập nhật' }}</span>
                </div>
                
                <div class="info-item">
                    <label>Email:</label>
                    <span>{{ $user->email }}</span>
                </div>
                
                <div class="info-item">
                    <label>Số điện thoại:</label>
                    <span>{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                </div>
                
                <div class="info-item">
                    <label>Giới tính:</label>
                    <span>{{ $user->gender_text }}</span>
                </div>
                
                <div class="info-item">
                    <label>Loại da:</label>
                    <span>{{ $user->skin_type_text }}</span>
                </div>
                
                <div class="info-item">
                    <label>Ngày sinh:</label>
                    <span>{{ $user->dob ? $user->dob->format('d/m/Y') : 'Chưa cập nhật' }}</span>
                </div>
            </div>
        </div>

        <!-- Ảnh đại diện -->
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-camera"></i> Ảnh đại diện</h2>
            </div>
            
            <div class="avatar-section">
                <div class="current-avatar">
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                         alt="Avatar" id="profileAvatar">
                </div>
                
                <div class="avatar-actions">
                    <label for="avatarInput" class="upload-btn">
                        <i class="fas fa-upload"></i> Thay đổi ảnh
                    </label>
                    <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                    <p class="avatar-hint">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</p>
                </div>
            </div>
        </div>

        <!-- Địa chỉ -->
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h2>
                <a href="{{ route('profile.addresses') }}" class="edit-btn">
                    <i class="fas fa-edit"></i> Quản lý địa chỉ
                </a>
            </div>
            
            <div class="address-info">
                @if($user->address || $user->city || $user->district || $user->ward)
                    <div class="address-details">
                        <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Xã/Phường:</strong> {{ $user->ward ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Quận/Huyện:</strong> {{ $user->district ?? 'Chưa cập nhật' }}</p>
                        <p><strong>Tỉnh/Thành phố:</strong> {{ $user->city ?? 'Chưa cập nhật' }}</p>
                    </div>
                @else
                    <div class="no-address">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Bạn chưa cập nhật địa chỉ giao hàng</p>
                        <a href="{{ route('profile.addresses') }}" class="add-address-btn">
                            <i class="fas fa-plus"></i> Thêm địa chỉ
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Các chức năng khác -->
        <div class="profile-actions">
            <a href="{{ route('profile.edit') }}" class="action-btn primary">
                <i class="fas fa-edit"></i> Chỉnh sửa thông tin
            </a>
            
            <a href="{{ route('profile.password') }}" class="action-btn secondary">
                <i class="fas fa-key"></i> Đổi mật khẩu
            </a>
            
            <a href="{{ route('orders.index') }}" class="action-btn secondary">
                <i class="fas fa-shopping-bag"></i> Xem đơn hàng
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.getElementById('profileAvatar');

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('avatar', file);

            fetch('{{ route("profile.update-avatar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    profileAvatar.src = data.avatar_url;
                    alert('Ảnh đại diện đã được cập nhật thành công!');
                } else {
                    alert('Có lỗi khi cập nhật ảnh đại diện: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating avatar:', error);
                alert('Có lỗi khi cập nhật ảnh đại diện.');
            });
        }
    });
});
</script>
@endsection
