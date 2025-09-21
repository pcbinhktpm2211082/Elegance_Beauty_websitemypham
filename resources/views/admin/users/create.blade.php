@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Thêm người dùng</h1>

    <div class="bg-white p-6 rounded shadow border">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tên -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Số điện thoại -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('password_confirmation') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>


            <!-- Avatar -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ảnh đại diện</label>
                <input type="file" name="avatar"
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('avatar') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Vai trò -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Vai trò</label>
                <select name="role" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị</option>
                </select>
            </div>

            <!-- Trạng thái -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Trạng thái</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Khoá</option>
                </select>
            </div>

            <!-- Nút -->
           <div class="flex justify-end" style="gap: 24px;">
                <a href="{{ route('admin.users.index') }}"
                    class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Hủy</a>
                <button type="submit"
                    class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection
