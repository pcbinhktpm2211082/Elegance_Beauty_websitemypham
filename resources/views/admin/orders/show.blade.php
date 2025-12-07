@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold mb-2">Chi tiết đơn hàng {{ $order->order_code }}</h1>
        <p class="text-gray-600">Xem và quản lý thông tin đơn hàng</p>
    </div>

    <!-- Nút quay lại -->
    <div class="mb-6 text-left">
        <a href="{{ route('admin.orders.index', request()->only(['status', 'search'])) }}"
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Thông tin khách hàng -->
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin khách hàng</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Họ và tên</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->customer_name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Số điện thoại</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->customer_phone }}</p>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Địa chỉ giao hàng</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->customer_address }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Phương thức thanh toán</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->payment_method_text }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Ghi chú</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->note ?? 'Không có' }}</p>
            </div>
        </div>
    </div>

    <!-- Sản phẩm trong đơn -->
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Sản phẩm trong đơn</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left">Sản phẩm</th>
                        <th class="border px-4 py-2 text-left">Biến thể</th>
                        <th class="border px-4 py-2 text-center">Số lượng</th>
                        <th class="border px-4 py-2 text-right">Đơn giá</th>
                        <th class="border px-4 py-2 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $item->product_name }}</td>
                        <td class="border px-4 py-2">{{ $item->variant_name ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $item->quantity }}</td>
                        <td class="border px-4 py-2 text-right font-mono">{{ number_format($item->unit_price, 0, ',', '.') }}₫</td>
                        <td class="border px-4 py-2 text-right font-mono">{{ number_format($item->total_price, 0, ',', '.') }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @php
            $subtotal = $order->subtotal;
            $shipping = max(0, (float)$order->total_price + (float)($order->discount_amount ?? 0) - (float)$subtotal);
        @endphp
        
        <div class="mt-6 bg-gray-50 p-4 rounded border">
            <div class="space-y-2 text-right">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Tạm tính:</span>
                    <span class="font-medium">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Giảm giá:</span>
                    <span class="font-medium text-green-600">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                </div>
                @if($order->voucher_code)
                <div class="text-right">
                    <span class="text-xs text-gray-500">(Mã: {{ $order->voucher_code }})</span>
                </div>
                @endif
                @endif
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Phí vận chuyển:</span>
                    <span class="font-medium">{{ number_format($shipping, 0, ',', '.') }}₫</span>
                </div>
                
                <div class="border-t border-gray-300 pt-2">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Thành tiền:</span>
                        <span class="text-xl font-bold text-blue-600">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cập nhật trạng thái -->
    <div class="bg-white p-4 rounded shadow border mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Cập nhật trạng thái đơn hàng</h3>
        
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
            @csrf
            <!-- Hidden input để giữ lại query params filter ban đầu (không phải status mới của đơn hàng) -->
            @if(request('status'))
                <input type="hidden" name="filter_status" value="{{ request('status') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Trạng thái mới</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" {{ $order->status=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ $order->status=='processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipped" {{ $order->status=='shipped' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="delivered" {{ $order->status=='delivered' ? 'selected' : '' }}>Đã hoàn thành</option>
                        <option value="cancelled" {{ $order->status=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">Ghi chú trạng thái</label>
                    <textarea name="note" placeholder="Ghi chú trạng thái (tuỳ chọn)" rows="3" 
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $order->note }}</textarea>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="inline-block px-4 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded hover:bg-blue-200 transition text-sm font-medium">
                    🔄 Cập nhật trạng thái
                </button>
            </div>
        </form>
    </div>

    <!-- Thông tin bổ sung -->
    <div class="bg-white p-4 rounded shadow border">
        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin bổ sung</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Trạng thái hiện tại</label>
                <div class="mt-1">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'shipped' => 'bg-purple-100 text-purple-800 border-purple-300',
                            'delivered' => 'bg-green-100 text-green-800 border-green-300',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                        ];
                        $statusLabels = [
                            'pending' => 'Chờ xử lý',
                            'processing' => 'Đang xử lý',
                            'shipped' => 'Đang giao hàng',
                            'delivered' => 'Đã hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];
                    @endphp
                    <span class="inline-block px-3 py-1 {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }} border rounded text-sm font-medium">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Ngày tạo đơn</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Cập nhật lần cuối</label>
                <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded border">{{ $order->updated_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
