@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Quản lý yêu cầu hỗ trợ</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
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

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Khách hàng</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Tiêu đề</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Ngày gửi</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supports as $support)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">#{{ $support->id }}</td>
                        <td class="border px-4 py-2">{{ $support->name }}</td>
                        <td class="border px-4 py-2">{{ $support->email }}</td>
                        <td class="border px-4 py-2">{{ $support->title }}</td>
                        <td class="border px-4 py-2">
                            @if($support->status == 'pending')
                                <span class="text-green-600 font-medium">Chờ xử lý</span>
                            @elseif($support->status == 'processing')
                                <span class="text-blue-600 font-medium">Đang xử lý</span>
                            @elseif($support->status == 'completed')
                                <span class="text-blue-600 font-medium">Đã hoàn thành</span>
                            @elseif($support->status == 'cancelled')
                                <span class="text-red-600 font-medium">Đã hủy</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $support->created_at->format('d/m/Y H:i') }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center flex-wrap gap-2">
                                <a href="{{ route('admin.supports.show', $support) }}"
                                   class="inline-block px-3 py-1 bg-blue-100 text-blue-700 border border-blue-300 rounded hover:bg-blue-200 transition text-xs font-medium">
                                    👁️ Xem
                                </a>
                                
                                @if($support->status == 'pending')
                                    <form method="POST" action="{{ route('admin.supports.processing', $support) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded hover:bg-yellow-200 transition text-xs font-medium">
                                            ⏳ Đang xử lý
                                        </button>
                                    </form>
                                @endif
                                
                                @if($support->status == 'pending' || $support->status == 'processing')
                                    <form method="POST" action="{{ route('admin.supports.done', $support) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-block px-3 py-1 bg-green-100 text-green-700 border border-green-300 rounded hover:bg-green-200 transition text-xs font-medium">
                                            ✅ Hoàn thành
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.supports.cancelled', $support) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-block px-3 py-1 bg-red-100 text-red-700 border border-red-300 rounded hover:bg-red-200 transition text-xs font-medium">
                                            ❌ Hủy
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border px-4 py-8 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có yêu cầu hỗ trợ</h3>
                            <p class="mt-1 text-sm text-gray-500">Chờ khách hàng gửi tin nhắn.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Phân trang -->
        @if($supports->hasPages())
            <div class="mt-4">
                {{ $supports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
