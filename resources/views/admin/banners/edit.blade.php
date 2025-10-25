@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Chỉnh sửa banner</h1>

    <div class="bg-white p-6 rounded shadow border">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh banner hiện tại</label>
                @if($banner->image)
                    <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" class="w-full max-w-md h-40 object-cover mb-2 rounded">
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
