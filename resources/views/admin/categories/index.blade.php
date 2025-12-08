@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <h1 class="text-xl font-bold text-center mb-2">Danh sách danh mục</h1>

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" placeholder="Tìm tên danh mục..." value="{{ request('search') }}" 
                   class="border border-gray-300 rounded px-3 py-1 flex-grow text-sm">

            <button type="submit" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Tìm kiếm
            </button>
            
            @if(request('search'))
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Làm mới
                </a>
            @endif
        </form>
    </div>

    <!-- Nút thêm -->
    <div class="mb-4 text-left">
        <a href="{{ route('admin.categories.create') }}"
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm danh mục
        </a>
    </div>

    <!-- Bảng -->
    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Tên danh mục</th>
                    <th class="border px-4 py-2">Ngày tạo</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2">{{ $category->id }}</td>
                    <td class="border px-4 py-2">{{ $category->name }}</td>
                    <td class="border px-4 py-2">{{ $category->created_at->format('d/m/Y') }}</td>
                    <td class="border px-4 py-2">
                        <div class="flex justify-center flex-wrap gap-2 items-center">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                Xem chi tiết
                            </a>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                Sửa
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
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

        <!-- Phân trang -->
        @if($categories->hasPages())
            <div class="mt-4">
                {{ $categories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
