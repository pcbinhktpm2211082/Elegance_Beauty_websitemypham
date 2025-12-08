@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Danh sách đơn hàng</h1>

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2 items-center">
            <select name="status" class="border border-gray-300 rounded px-3 py-1 text-sm">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="processing" {{ request('status')=='processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="shipped" {{ request('status')=='shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="delivered" {{ request('status')=='delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>

            <input type="text" name="search" placeholder="Tìm mã đơn, khách..." value="{{ request('search') }}" 
                   class="border border-gray-300 rounded px-3 py-1 flex-grow text-sm">

            <button type="submit" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Tìm kiếm
            </button>
        </form>
    </div>

    @php
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
        ];
        
        $statusColors = [
            'pending' => 'text-yellow-600 font-medium',
            'processing' => 'text-blue-600 font-medium',
            'shipped' => 'text-purple-600 font-medium',
            'delivered' => 'text-green-600 font-medium',
            'cancelled' => 'text-red-600 font-medium',
        ];
    @endphp

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Mã đơn</th>
                    <th class="border px-4 py-2">Khách hàng</th>
                    <th class="border px-4 py-2">Tổng tiền</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Ngày tạo</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2 text-left">{{ $order->order_code }}</td>
                    <td class="border px-4 py-2 text-left">{{ $order->customer_name }}</td>
                    <td class="border px-4 py-2 text-right font-mono">{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                    <td class="border px-4 py-2">
                        <span class="{{ $statusColors[$order->status] ?? 'text-gray-600' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="border px-4 py-2">
                        <div class="flex justify-center flex-wrap gap-2 items-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                               Xem
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Bạn chắc chắn muốn xoá đơn này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border px-4 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có đơn hàng nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Chờ khách hàng đặt hàng.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Phân trang -->
        @if($orders->hasPages())
            <div class="mt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
