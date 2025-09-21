@extends('layouts.user')

@section('title', 'Quản lý địa chỉ giao hàng')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>Quản lý địa chỉ giao hàng</h1>
        <p>Thêm và quản lý các địa chỉ giao hàng của bạn</p>
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
                <h2><i class="fas fa-map-marker-alt"></i> Địa chỉ hiện tại</h2>
                <a href="{{ route('profile.show') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            <div class="current-address">
                @if($user->address || $user->city || $user->district || $user->ward)
                    <div class="address-card">
                        <div class="address-header">
                            <h3><i class="fas fa-home"></i> Địa chỉ chính</h3>
                            <span class="address-type">Mặc định</span>
                        </div>
                        
                        <div class="address-content">
                            <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Chưa cập nhật' }}</p>
                            <p><strong>Xã/Phường:</strong> {{ $user->ward ?? 'Chưa cập nhật' }}</p>
                            <p><strong>Quận/Huyện:</strong> {{ $user->district ?? 'Chưa cập nhật' }}</p>
                            <p><strong>Tỉnh/Thành phố:</strong> {{ $user->city ?? 'Chưa cập nhật' }}</p>
                        </div>
                        
                        <div class="address-actions">
                            <a href="{{ route('profile.edit') }}" class="edit-address-btn">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                        </div>
                    </div>
                @else
                    <div class="no-address">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Bạn chưa có địa chỉ giao hàng nào</p>
                        <a href="{{ route('profile.edit') }}" class="add-address-btn">
                            <i class="fas fa-plus"></i> Thêm địa chỉ đầu tiên
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-info-circle"></i> Hướng dẫn</h2>
            </div>
            
            <div class="address-guide">
                <div class="guide-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4>Địa chỉ chính</h4>
                        <p>Đây là địa chỉ mặc định sẽ được sử dụng khi giao hàng. Bạn có thể chỉnh sửa thông tin này.</p>
                    </div>
                </div>
                
                <div class="guide-item">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <h4>Lưu ý khi nhập địa chỉ</h4>
                        <ul>
                            <li>Nhập địa chỉ chi tiết: số nhà, tên đường, phường/xã</li>
                            <li>Chọn đúng quận/huyện và tỉnh/thành phố</li>
                            <li>Địa chỉ càng chi tiết càng dễ giao hàng</li>
                            <li>Cập nhật địa chỉ khi có thay đổi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
