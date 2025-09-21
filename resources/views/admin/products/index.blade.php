@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">Danh sách sản phẩm</h1>

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
                    <th class="border px-4 py-2">Nổi bật</th> <!-- Cột mới -->
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
                        <td class="border px-4 py-2">
                            <div class="flex justify-center flex-wrap gap-4">
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded hover:bg-yellow-200 transition text-xs font-medium">
                                   ✏️ Sửa
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-block px-3 py-1 bg-red-100 text-red-700 border border-red-300 rounded hover:bg-red-200 transition text-xs font-medium">
                                            🗑️ Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
