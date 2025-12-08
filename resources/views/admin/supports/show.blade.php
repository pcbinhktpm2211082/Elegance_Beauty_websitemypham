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
    <div class="mb-4 text-left">
        <a href="{{ route('admin.supports.index') }}" 
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Support Details -->
        <div class="bg-white p-4 rounded shadow border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin khách hàng</h3>
        
                <div class="mb-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Trạng thái cuộc trò chuyện</label>
                    @if($support->status == 'pending')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 text-green-800 border border-green-200 rounded text-sm font-medium">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span> Chờ phản hồi
                        </span>
                    @elseif($support->status == 'processing')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-800 border border-blue-200 rounded text-sm font-medium">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span> Đang trao đổi
                        </span>
                    @elseif($support->status == 'completed')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded text-sm font-medium">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span> Đã hoàn thành
                        </span>
                    @elseif($support->status == 'cancelled')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 text-red-800 border border-red-200 rounded text-sm font-medium">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span> Đã hủy
                        </span>
                    @endif
            </div>
            
            <div class="space-y-4 text-sm">
                    <div>
                    <label class="block text-gray-500 mb-1">Họ và tên</label>
                    <p class="text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->name }}</p>
                    </div>
                    <div>
                    <label class="block text-gray-500 mb-1">Email</label>
                    <p class="text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->email }}</p>
                    </div>
                    <div>
                    <label class="block text-gray-500 mb-1">Tiêu đề</label>
                    <p class="text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->title }}</p>
                    </div>
                    <div>
                    <label class="block text-gray-500 mb-1">Bắt đầu</label>
                    <p class="text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-gray-500 mb-1">Cập nhật gần nhất</label>
                    <p class="text-gray-900 bg-gray-50 p-2 rounded border">{{ $support->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        
            <div class="mt-6 space-y-3">
            @if($support->status == 'pending')
                <form method="POST" action="{{ route('admin.supports.processing', $support) }}">
                    @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-medium">
                        Đánh dấu đang xử lý
                    </button>
                </form>
            @endif
            
            @if($support->status == 'pending' || $support->status == 'processing')
                <form method="POST" action="{{ route('admin.supports.done', $support) }}">
                    @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-medium">
                        Đánh dấu hoàn thành
                    </button>
                </form>
                
                <form method="POST" action="{{ route('admin.supports.cancelled', $support) }}">
                    @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-medium">
                            Hủy cuộc trò chuyện
                    </button>
                </form>
            @endif
            </div>
        </div>

        <!-- Chat -->
        <div class="bg-white p-4 rounded shadow border lg:col-span-2 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Lịch sử tin nhắn</h3>

            <div class="flex-1 overflow-y-auto mb-4 space-y-3 max-h-[450px]">
                @forelse($messages as $message)
                    <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xl rounded-2xl px-4 py-3 {{ $message->is_admin ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                            <p class="text-sm">{{ $message->message }}</p>
                            <p class="text-xs mt-2 {{ $message->is_admin ? 'text-indigo-100' : 'text-gray-500' }}">
                                {{ $message->is_admin ? 'Admin' : $support->name }} • {{ $message->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-10">
                        Chưa có tin nhắn nào trong cuộc trò chuyện này.
                    </div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.supports.messages.store', $support) }}" class="space-y-3">
                @csrf
                <textarea name="message" rows="4" class="w-full border border-gray-300 rounded-xl p-3 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Nhập phản hồi của bạn...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror

                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Giữ nguyên trạng thái</option>
                        <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Đánh dấu hoàn thành</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Đánh dấu chờ xử lý</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đánh dấu đã hủy</option>
                    </select>

                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-9.193 5.495A.75.75 0 014.5 16.06V7.94a.75.75 0 011.059-.603l9.193 5.495a.75.75 0 010 1.297z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 5.25v13.5" />
                        </svg>
                        Gửi phản hồi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
