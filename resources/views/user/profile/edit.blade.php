@extends('layouts.user')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <h1>Chỉnh sửa thông tin cá nhân</h1>
        <p>Cập nhật thông tin tài khoản của bạn</p>
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
                <h2><i class="fas fa-edit"></i> Thông tin cá nhân</h2>
                <a href="{{ route('profile.show') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            
            <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Họ và tên *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               class="form-input @error('name') error @enderror"
                               placeholder="Nhập họ và tên đầy đủ">
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="form-input @error('email') error @enderror"
                               placeholder="Email của bạn">
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               class="form-input @error('phone') error @enderror"
                               placeholder="Số điện thoại liên hệ">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gender">Giới tính</label>
                        <select id="gender" name="gender" class="form-select @error('gender') error @enderror">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="skin_type">Loại da</label>
                        <select id="skin_type" name="skin_type" class="form-select @error('skin_type') error @enderror">
                            <option value="">Chọn loại da</option>
                            <option value="normal" {{ old('skin_type', $user->skin_type) == 'normal' ? 'selected' : '' }}>Da Thường</option>
                            <option value="dry" {{ old('skin_type', $user->skin_type) == 'dry' ? 'selected' : '' }}>Da Khô</option>
                            <option value="oily" {{ old('skin_type', $user->skin_type) == 'oily' ? 'selected' : '' }}>Da Dầu/Nhờn</option>
                            <option value="combination" {{ old('skin_type', $user->skin_type) == 'combination' ? 'selected' : '' }}>Da Hỗn Hợp</option>
                            <option value="sensitive" {{ old('skin_type', $user->skin_type) == 'sensitive' ? 'selected' : '' }}>Da Nhạy Cảm</option>
                        </select>
                        @error('skin_type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <div class="mt-2">
                            <a href="{{ route('skin-quiz.show') }}" class="text-blue-600 hover:text-blue-800 underline text-sm">
                                📝 Quiz Phân Loại Da
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dob">Ngày sinh</label>
                        <input type="date" 
                               id="dob" 
                               name="dob" 
                               value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" 
                               class="form-input @error('dob') error @enderror">
                        @error('dob')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="address-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h3>
                    
                    <div class="form-group">
                        <label for="address">Địa chỉ chi tiết</label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="form-textarea @error('address') error @enderror"
                                  placeholder="Nhập địa chỉ chi tiết (số nhà, tên đường, phường/xã)">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="address-grid">
                        <div class="form-group">
                            <label for="ward">Xã/Phường</label>
                            <input type="text" 
                                   id="ward" 
                                   name="ward" 
                                   value="{{ old('ward', $user->ward) }}" 
                                   class="form-input @error('ward') error @enderror"
                                   placeholder="Xã/Phường">
                            @error('ward')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="district">Quận/Huyện</label>
                            <input type="text" 
                                   id="district" 
                                   name="district" 
                                   value="{{ old('district', $user->district) }}" 
                                   class="form-input @error('district') error @enderror"
                                   placeholder="Quận/Huyện">
                            @error('district')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="city">Tỉnh/Thành phố</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $user->city) }}" 
                                   class="form-input @error('city') error @enderror"
                                   placeholder="Tỉnh/Thành phố">
                            @error('city')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i>
                        Cập nhật thông tin
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
