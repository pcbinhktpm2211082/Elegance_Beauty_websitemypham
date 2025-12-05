@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold mb-4 text-center">Quản lý đánh giá sản phẩm</h1>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.reviews.index') }}" class="mb-4 flex flex-wrap gap-2 items-center">
        <input type="text" name="product" value="{{ request('product') }}" placeholder="Tìm theo tên sản phẩm..."
               class="border border-gray-300 rounded px-3 py-1 text-sm flex-1 min-w-[200px]">
        <select name="rating" class="border border-gray-300 rounded px-3 py-1 text-sm">
            <option value="">Tất cả số sao</option>
            @for($i=5;$i>=1;$i--)
                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">Lọc</button>
        @if(request('product') || request('rating'))
            <a href="{{ route('admin.reviews.index') }}" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-600 hover:bg-gray-50">Đặt lại</a>
        @endif
    </form>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full table-auto text-sm text-center border-collapse">
            <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">ID</th>
                <th class="border px-3 py-2">Sản phẩm</th>
                <th class="border px-3 py-2">Người dùng</th>
                <th class="border px-3 py-2">Sao</th>
                <th class="border px-3 py-2">Nhận xét & phản hồi</th>
                <th class="border px-3 py-2">Ngày</th>
                <th class="border px-3 py-2">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2">#{{ $review->id }}</td>
                    <td class="border px-3 py-2 text-left">
                        {{ $review->product->name ?? 'N/A' }}
                    </td>
                    <td class="border px-3 py-2">
                        {{ $review->user->name ?? 'N/A' }}
                    </td>
                    <td class="border px-3 py-2">
                        @for($i=1;$i<=5;$i++)
                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                        @endfor
                    </td>
                    <td class="border px-3 py-2 text-left max-w-xs">
                        <div class="text-sm text-gray-800">
                            {{ \Illuminate\Support\Str::limit($review->comment, 80) ?: '—' }}
                        </div>

                        @if($review->admin_reply)
                            <div class="mt-2 px-2 py-1.5 rounded-md bg-indigo-50 border border-indigo-100 text-xs text-gray-700">
                                <div class="font-semibold text-[11px] text-indigo-700 mb-0.5">
                                    Phản hồi của cửa hàng
                                    @if($review->admin_replied_at)
                                        @php
                                            $repliedAt = $review->admin_replied_at instanceof \Illuminate\Support\Carbon
                                                ? $review->admin_replied_at
                                                : \Illuminate\Support\Carbon::parse($review->admin_replied_at);
                                        @endphp
                                        <span class="text-[10px] text-gray-400">
                                            ({{ $repliedAt->format('d/m/Y H:i') }})
                                        </span>
                                    @endif
                                </div>
                                <div>{{ \Illuminate\Support\Str::limit($review->admin_reply, 120) }}</div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="mt-2 space-y-1">
                            @csrf
                            <textarea name="admin_reply"
                                      rows="2"
                                      class="w-full border border-gray-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-300 focus:border-indigo-300"
                                      placeholder="Nhập phản hồi cho đánh giá này...">{{ old('admin_reply', $review->admin_reply) }}</textarea>
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-3 py-1 rounded-md text-xs font-semibold"
                                        style="background: linear-gradient(135deg,#4f46e5,#6366f1); color:#ffffff;">
                                    Lưu phản hồi
                                </button>
                            </div>
                        </form>
                    </td>
                    <td class="border px-3 py-2">
                        {{ $review->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="border px-3 py-2">
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Xoá đánh giá này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-50 text-red-600 border border-red-200 rounded text-xs hover:bg-red-100">
                                Xoá
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-3 py-6 text-gray-500">Chưa có đánh giá nào.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($reviews->hasPages())
            <div class="p-3">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


