@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold mb-2">Chi tiết yêu cầu hỗ trợ #{{ $support->id }}</h1>
        <p class="text-gray-600">Xem và xử lý yêu cầu hỗ trợ từ khách hàng</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Nút quay lại -->
    <div class="mb-6 text-left">
        <a href="{{ route('admin.supports.index') }}" 
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Support Details -->
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin yêu cầu hỗ trợ</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-500 mb-1">Trạng thái</label>
                <div class="mb-4">
                    @if($support->status == 'pending')
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-300 rounded text-sm font-medium">
                            Chờ xử lý
                        </span>
                    @elseif($support->status == 'processing')
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 rounded text-sm font-medium">
                            Đang xử lý
                        </span>
                    @elseif($support->status == 'completed')
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 rounded text-sm font-medium">
                            Đã hoàn thành
                        </span>
                    @elseif($support->status == 'cancelled')
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 border border-red-300 rounded text-sm font-medium">
                            Đã hủy
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="md:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Họ và tên</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tiêu đề</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ngày gửi</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
                
                @if($support->updated_at != $support->created_at)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Cập nhật lần cuối</label>
                    <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-500 mb-2">Nội dung tin nhắn</label>
            <div class="bg-gray-50 p-4 rounded border">
                <p class="whitespace-pre-wrap text-sm text-gray-900">{{ $support->message }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thao tác</h3>
        
        <div class="flex flex-wrap gap-3">
            @if($support->status == 'pending')
                <form method="POST" action="{{ route('admin.supports.processing', $support) }}">
                    @csrf
                    <button type="submit" class="inline-block px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded hover:bg-green-200 transition text-sm font-medium">
                        ⏳ Đánh dấu đang xử lý
                    </button>
                </form>
            @endif
            
            @if($support->status == 'pending' || $support->status == 'processing')
                <form method="POST" action="{{ route('admin.supports.done', $support) }}">
                    @csrf
                    <button type="submit" class="inline-block px-4 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded hover:bg-blue-200 transition text-sm font-medium">
                        ✅ Đánh dấu hoàn thành
                    </button>
                </form>
                
                <form method="POST" action="{{ route('admin.supports.cancelled', $support) }}">
                    @csrf
                    <button type="submit" class="inline-block px-4 py-2 bg-red-100 text-red-700 border border-red-300 rounded hover:bg-red-200 transition text-sm font-medium">
                        ❌ Hủy yêu cầu
                    </button>
                </form>
            @endif
            
            @if($support->status == 'completed')
                <span class="inline-block px-4 py-2 bg-blue-100 text-blue-800 border border-blue-300 rounded text-sm font-medium">
                    ✅ Đã hoàn thành
                </span>
            @endif
            
            @if($support->status == 'cancelled')
                <span class="inline-block px-4 py-2 bg-red-100 text-red-800 border border-red-300 rounded text-sm font-medium">
                    ❌ Đã hủy
                </span>
            @endif
        </div>
    </div>
</div>
@endsection
