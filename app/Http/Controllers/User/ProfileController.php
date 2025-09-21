<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('user.profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'phone.max' => 'Số điện thoại không được quá 20 ký tự',
            'gender.in' => 'Giới tính không hợp lệ',
            'dob.before' => 'Ngày sinh phải trước ngày hôm nay',
            'address.max' => 'Địa chỉ không được quá 500 ký tự',
            'city.max' => 'Tỉnh/Thành phố không được quá 100 ký tự',
            'district.max' => 'Quận/Huyện không được quá 100 ký tự',
            'ward.max' => 'Xã/Phường không được quá 100 ký tự',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
        ]);

        return redirect()->route('profile.show')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh',
            'avatar.image' => 'File phải là ảnh',
            'avatar.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif',
            'avatar.max' => 'Ảnh không được lớn hơn 2MB'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
        
        // Xóa ảnh cũ nếu có
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Lưu ảnh mới
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'avatar_url' => asset('storage/' . $avatarPath),
            'message' => 'Ảnh đại diện đã được cập nhật thành công!'
        ]);
    }

    public function changePassword()
    {
        return view('user.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.password')->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }

    public function addresses()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('user.profile.addresses', compact('user'));
    }
}
