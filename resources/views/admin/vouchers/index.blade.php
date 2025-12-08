@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Danh sách voucher</h1>

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" placeholder="Tìm mã hoặc mô tả voucher..." value="{{ request('search') }}" 
                   class="border border-gray-300 rounded px-3 py-1 flex-grow text-sm">

            <button type="submit" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Tìm kiếm
            </button>
            
            @if(request('search'))
                <a href="{{ route('admin.vouchers.index') }}" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Làm mới
                </a>
            @endif
        </form>
    </div>

    <!-- Nút thêm -->
    <div class="mb-4 text-left">
        <a href="{{ route('admin.vouchers.create') }}"
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm voucher
        </a>
    </div>

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Mã</th>
                    <th class="border px-4 py-2">Mô tả</th>
                    <th class="border px-4 py-2">Giảm</th>
                    <th class="border px-4 py-2">Đơn tối thiểu</th>
                    <th class="border px-4 py-2">Thời gian</th>
                    <th class="border px-4 py-2">Giới hạn</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @foreach($vouchers as $voucher)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2 font-mono">{{ $voucher->code }}</td>
                    <td class="border px-4 py-2 text-left">{{ $voucher->description }}</td>
                    <td class="border px-4 py-2">{{ $voucher->discount_type === 'percent' ? $voucher->discount_value . '%' : number_format($voucher->discount_value) . '₫' }}</td>
                    <td class="border px-4 py-2">{{ $voucher->min_order_amount ? number_format($voucher->min_order_amount) . '₫' : '-' }}</td>
                    <td class="border px-4 py-2">{{ optional($voucher->start_date)->format('d/m/Y') }} - {{ optional($voucher->end_date)->format('d/m/Y') }}</td>
                    <td class="border px-4 py-2">{{ $voucher->usage_limit ?? '∞' }} / {{ $voucher->used_count }}</td>
                    <td class="border px-4 py-2">
                        @php
                            $statusHtml = '';
                            if (!$voucher->is_active) {
                                $statusHtml = '<span class="text-gray-500">Đang tắt</span>';
                            } elseif ($voucher->start_date && now()->lt($voucher->start_date)) {
                                $statusHtml = '<span class="text-blue-600">Chưa đến ngày</span>';
                            } elseif ($voucher->end_date && now()->gt($voucher->end_date)) {
                                $statusHtml = '<span class="text-gray-500">Hết hạn</span>';
                            } elseif (!is_null($voucher->usage_limit) && $voucher->used_count >= $voucher->usage_limit) {
                                $statusHtml = '<span class="text-gray-500">Hết lượt</span>';
                            } else {
                                $statusHtml = '<span class="text-green-600 font-semibold">Đang hiệu lực</span>';
                            }
                        @endphp
                        {!! $statusHtml !!}
                    </td>
                    <td class="border px-4 py-2">
                        <div class="flex justify-center flex-wrap gap-2 items-center">
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" 
                               class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                               Sửa
                            </a>
                            <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" onsubmit="return confirm('Xoá voucher?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <!-- Phân trang -->
        @if($vouchers->hasPages())
            <div class="mt-4">
                {{ $vouchers->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection



