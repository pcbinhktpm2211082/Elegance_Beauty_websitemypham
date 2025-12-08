@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Chỉnh sửa loại sản phẩm</h1>

    <div class="bg-white p-6 rounded shadow border">
        <form method="POST" action="{{ route('admin.product-types.update', $productType->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên loại sản phẩm: <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $productType->name) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="VD: Exfoliator, Tẩy tế bào chết, etc.">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="requires_skin_type_filter" value="1" 
                           {{ old('requires_skin_type_filter', $productType->requires_skin_type_filter) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <span class="ml-2 text-sm font-semibold text-gray-800">
                        Cần áp dụng bộ lọc loại da (Da Dầu/Khô)
                    </span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">
                    Nếu bỏ chọn, sản phẩm thuộc loại này sẽ bỏ qua bộ lọc loại da trong hệ thống gợi ý.
                </p>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.product-types.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Quay lại
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

