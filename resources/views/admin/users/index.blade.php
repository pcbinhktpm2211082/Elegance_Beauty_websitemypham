@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Quản lý khách hàng</h1>

    <!-- Thông báo -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2 items-center">
            <select name="status" class="border border-gray-300 rounded px-3 py-1 text-sm">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã khóa</option>
            </select>

            <input type="text" name="search" placeholder="Tìm tên hoặc email..." value="{{ request('search') }}" 
                   class="border border-gray-300 rounded px-3 py-1 flex-grow text-sm">

            <button type="submit" class="px-4 py-1 bg-blue-100 text-blue-700 border border-blue-300 rounded hover:bg-blue-200 transition text-sm font-semibold">
                Tìm kiếm
            </button>
            
            @if(request('search') || request('status'))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    🔄 Làm mới
                </a>
            @endif
        </form>
    </div>

    <!-- Nút thêm -->
    <div class="mb-4 text-left">
        <a href="{{ route('admin.users.create') }}"
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm người dùng
        </a>
    </div>

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Ảnh</th>
                    <th class="border px-4 py-2">Tên</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Số điện thoại</th>
                    <th class="border px-4 py-2">Vai trò</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $user->id }}</td>
                        <td class="border px-4 py-2">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}?v={{ $user->updated_at->timestamp }}" 
                                alt="Avatar" 
                                class="w-12 h-12 object-cover rounded-full mx-auto"
                                style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <span class="text-gray-400 italic">Không có</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->phone ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $user->role }}</td>
                        <td class="border px-4 py-2">
                            @if($user->status)
                                <span class="text-green-600 font-medium">Hoạt động</span>
                            @else
                                <span class="text-red-600 font-medium">Khoá</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center flex-wrap gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="inline-block px-3 py-1 bg-blue-100 text-blue-700 border border-blue-300 rounded hover:bg-blue-200 transition text-xs font-medium">
                                    👁️ Xem
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded hover:bg-yellow-200 transition text-xs font-medium">
                                    ✏️ Sửa
                                </a>
                                
                                <!-- Nút khóa/mở khóa -->
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-block px-3 py-1 {{ $user->status ? 'bg-red-100 text-red-700 border-red-300 hover:bg-red-200' : 'bg-green-100 text-green-700 border-green-300 hover:bg-green-200' }} border rounded transition text-xs font-medium"
                                            onclick="return confirm('Bạn có chắc chắn muốn {{ $user->status ? 'khóa' : 'mở khóa' }} tài khoản này?')">
                                        {{ $user->status ? '🔒 Khóa' : '🔓 Mở khóa' }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xoá?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-block px-3 py-1 bg-red-100 text-red-700 border border-red-300 rounded hover:bg-red-200 transition text-xs font-medium">
                                        🗑️ Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
