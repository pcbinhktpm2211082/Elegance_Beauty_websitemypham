@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-2">Danh sách sản phẩm</h1>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-2 items-center">
            <select name="category_id" class="border border-gray-300 rounded px-3 py-1 text-sm">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="search" placeholder="Tìm tên sản phẩm..." value="{{ request('search') }}" 
                   class="border border-gray-300 rounded px-3 py-1 flex-grow text-sm">

            <button type="submit" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Tìm kiếm
            </button>
            
            @if(request('search') || request('category_id'))
                <a href="{{ route('admin.products.index') }}" class="px-4 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Làm mới
                </a>
            @endif
        </form>
    </div>

    <div class="mb-4">
        <a href="{{ route('admin.products.create') }}" 
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm sản phẩm
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Ảnh</th>
                    <th class="border px-4 py-2">Tên sản phẩm</th>
                    <th class="border px-4 py-2">Giá</th>
                    <th class="border px-4 py-2">Số lượng</th>
                    <th class="border px-4 py-2">Danh mục</th>
                    <th class="border px-4 py-2">Nổi bật</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $product->id }}</td>
                        <td class="border px-4 py-2">
                            @php
                                $cover = $product->coverOrFirstImage;
                            @endphp
                            <div class="w-20 h-20 rounded overflow-hidden bg-gray-100 border border-gray-200 mx-auto">
                                <img src="{{ $cover ? asset('storage/' . $cover) : asset('storage/placeholder.jpg') }}" alt="Ảnh bìa sản phẩm" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="border px-4 py-2">{{ $product->name }}</td>
                        <td class="border px-4 py-2">{{ number_format($product->price) }}₫</td>
                        <td class="border px-4 py-2">{{ $product->quantity }}</td>
                        <td class="border px-4 py-2">{{ $product->category->name ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">
                            @if ($product->is_featured)
                                <span class="text-green-600 font-semibold">Có</span>
                            @else
                                <span class="text-gray-500">Không</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2 text-center">
                            @if ($product->is_active)
                                <span class="text-green-600 font-semibold">Kích hoạt</span>
                            @else
                                <span class="text-red-600 font-semibold">Vô hiệu hóa</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center flex-wrap gap-2 items-center">
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="inline-block px-3 py-1 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                   Sửa
                                </a>
                                <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-block px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200 border rounded transition text-xs font-medium whitespace-nowrap flex-shrink-0"
                                            style="white-space: nowrap !important;">
                                            {{ $product->is_active ? 'Vô hiệu' : 'Kích hoạt' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá?')" class="inline">
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
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        @if($products->hasPages())
            <div class="mt-4">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
