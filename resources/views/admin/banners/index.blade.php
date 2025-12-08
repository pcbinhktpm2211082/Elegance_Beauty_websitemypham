@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-2">Quản lý banner</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.banners.create') }}" 
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm banner
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Ảnh</th>
                    <th class="border px-4 py-2">Tiêu đề</th>
                    <th class="border px-4 py-2">Vị trí</th>
                    <th class="border px-4 py-2">Thứ tự</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banners as $banner)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2">{{ $banner->id }}</td>
                    <td class="border px-4 py-2">
                        @if($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner" class="w-24 h-16 object-cover mx-auto rounded">
                        @else
                            <span class="text-gray-400 italic">Không có</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2 text-left">{{ $banner->title ?? 'Không có tiêu đề' }}</td>
                    <td class="border px-4 py-2">
                        @switch($banner->position)
                            @case('left')
                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 border border-blue-200">Slider trái</span>
                                @break
                            @case('right_top')
                                <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-700 border border-purple-200">Phải - Trên</span>
                                @break
                            @case('right_bottom')
                                <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-700 border border-purple-200">Phải - Dưới</span>
                                @break
                            @default
                                <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-700 border border-gray-200">Không xác định</span>
                        @endswitch
                    </td>
                    <td class="border px-4 py-2">{{ $banner->order }}</td>
                    <td class="border px-4 py-2">
                        @if($banner->is_active)
                            <span class="text-green-600 font-medium">Hiển thị</span>
                        @else
                            <span class="text-red-600 font-medium">Ẩn</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <div class="flex justify-center flex-wrap gap-2 items-center">
                            <a href="{{ route('admin.banners.edit', $banner) }}" 
                               class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                               Sửa
                            </a>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($banners->hasPages())
            <div class="mt-4">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
