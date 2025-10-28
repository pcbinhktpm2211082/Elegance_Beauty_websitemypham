@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Chỉnh sửa banner</h1>

    <div class="bg-white p-6 rounded shadow border">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
            <div class="mb-4 p-3 rounded border border-red-300 bg-red-50 text-red-700 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $banner->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí hiển thị <span class="text-red-500">*</span></label>
                <select name="position" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="left" {{ old('position', $banner->position) === 'left' ? 'selected' : '' }}>Slider bên trái</option>
                    <option value="right_top" {{ old('position', $banner->position) === 'right_top' ? 'selected' : '' }}>Banner phải - Trên</option>
                    <option value="right_bottom" {{ old('position', $banner->position) === 'right_bottom' ? 'selected' : '' }}>Banner phải - Dưới</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh banner hiện tại</label>
                @if($banner->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" 
                             class="max-w-xs max-h-48 w-auto h-auto object-contain border border-gray-300 rounded shadow-sm">
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn ảnh mới (để trống nếu không muốn thay đổi)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Kích thước khuyên dùng: 1200x400px</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link (URL)</label>
                <input type="url" name="link" value="{{ old('link', $banner->link) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">URL để chuyển hướng khi click vào banner</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
                <input type="number" name="order" value="{{ old('order', $banner->order) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Số nhỏ hơn sẽ hiển thị trước</p>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Hiển thị banner</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Cập nhật
                </button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
