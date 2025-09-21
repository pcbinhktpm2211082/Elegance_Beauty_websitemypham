@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold mb-2">Thông tin người dùng</h1>
        <p class="text-gray-600">Xem chi tiết thông tin người dùng</p>
    </div>

    <!-- Nút quay lại -->
    <div class="mb-6 text-left">
        <a href="{{ route('admin.users.index') }}"
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Thông tin người dùng -->
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin cá nhân</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Avatar -->
            <div class="md:col-span-1 flex justify-center">
                @if ($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}?v={{ $user->updated_at->timestamp }}" 
                         alt="Avatar"
                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg">
                @else
                    <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-4xl font-bold border-4 border-gray-300">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Thông tin cơ bản -->
            <div class="md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Họ và tên</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Số điện thoại</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->phone ?? 'Không có' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Vai trò</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->role }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Trạng thái</label>
                        <div class="mt-1">
                            @if($user->status)
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-300 rounded text-sm font-medium">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-800 border border-red-300 rounded text-sm font-medium">
                                    Khoá
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ngày tạo</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                
                @if($user->updated_at != $user->created_at)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Cập nhật lần cuối</label>
                    <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Thống kê đơn hàng (nếu có) -->
    @if(isset($user->orders) && $user->orders->count() > 0)
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thống kê đơn hàng</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-blue-50 rounded border">
                <div class="text-2xl font-bold text-blue-600">{{ $user->orders->count() }}</div>
                <div class="text-sm text-blue-600">Tổng đơn hàng</div>
            </div>
            
            <div class="text-center p-3 bg-yellow-50 rounded border">
                <div class="text-2xl font-bold text-yellow-600">{{ $user->orders->where('status', 'pending')->count() }}</div>
                <div class="text-sm text-yellow-600">Chờ xử lý</div>
            </div>
            
            <div class="text-center p-3 bg-green-50 rounded border">
                <div class="text-2xl font-bold text-green-600">{{ $user->orders->where('status', 'delivered')->count() }}</div>
                <div class="text-sm text-green-600">Đã giao</div>
            </div>
            
            <div class="text-center p-3 bg-red-50 rounded border">
                <div class="text-2xl font-bold text-red-600">{{ $user->orders->where('status', 'cancelled')->count() }}</div>
                <div class="text-sm text-red-600">Đã hủy</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Thao tác -->
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thao tác</h3>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}"
               class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded hover:bg-yellow-200 transition text-sm font-medium">
                ✏️ Sửa thông tin
            </a>
            
            @if($user->status)
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline-block"
                      onsubmit="return confirm('Bạn có chắc chắn muốn khoá người dùng này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-block px-4 py-2 bg-red-100 text-red-700 border border-red-300 rounded hover:bg-red-200 transition text-sm font-medium">
                        🔒 Khoá người dùng
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="inline-block">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="1">
                    <button type="submit"
                            class="inline-block px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded hover:bg-green-200 transition text-sm font-medium">
                        🔓 Mở khoá
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
