@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Thêm voucher</h1>

    <div class="bg-white p-6 rounded shadow border">
        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf
            <!-- Mã voucher -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mã voucher</label>
                <input name="code" value="{{ old('code') }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
                @error('code') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Mô tả -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <input name="description" value="{{ old('description') }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('description') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <!-- Loại giảm và Giá trị giảm -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Loại giảm</label>
                    <select name="discount_type" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Tiền (₫)</option>
                    </select>
                    @error('discount_type') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Giá trị giảm</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" required>
                    @error('discount_value') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>
            <!-- Đơn tối thiểu -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Đơn tối thiểu (₫)</label>
                <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount') }}" 
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                @error('min_order_amount') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <!-- Thời gian hiệu lực -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Bắt đầu</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('start_date') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kết thúc</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                    @error('end_date') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>
            <!-- Giới hạn sử dụng và Trạng thái -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Giới hạn sử dụng</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" 
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500" 
                           placeholder="Để trống = không giới hạn">
                    @error('usage_limit') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" 
                               class="border border-gray-300 rounded" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm font-medium">Kích hoạt voucher</label>
                    </div>
                    @error('is_active') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>
            <!-- Nút -->
            <div class="flex justify-end" style="gap: 24px;">
                <a href="{{ route('admin.vouchers.index') }}"
                    class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Hủy</a>
                <button type="submit"
                    class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection


