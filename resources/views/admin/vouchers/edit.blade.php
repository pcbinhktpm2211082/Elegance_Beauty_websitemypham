@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <h1 class="text-xl font-bold mb-4">Sửa voucher</h1>
    <div class="bg-white p-4 rounded shadow border">
        <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium">Mã voucher</label>
                <input name="code" value="{{ old('code', $voucher->code) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Mô tả</label>
                <input name="description" value="{{ old('description', $voucher->description) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">Loại giảm</label>
                    <select name="discount_type" class="w-full border rounded px-3 py-2">
                        <option value="percent" {{ $voucher->discount_type==='percent'?'selected':'' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ $voucher->discount_type==='fixed'?'selected':'' }}>Tiền (₫)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Giá trị giảm</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $voucher->discount_value) }}" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Đơn tối thiểu (₫)</label>
                <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">Bắt đầu</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', optional($voucher->start_date)->format('Y-m-d\TH:i')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Kết thúc</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date', optional($voucher->end_date)->format('Y-m-d\TH:i')) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex items-end gap-2">
                    <input type="checkbox" name="is_active" id="is_active" class="border" {{ $voucher->is_active ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm">Kích hoạt</label>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-gray-800 text-white rounded">Cập nhật</button>
                <a href="{{ route('admin.vouchers.index') }}" class="px-4 py-2 border rounded">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection


