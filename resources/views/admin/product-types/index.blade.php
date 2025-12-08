@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-2">Quản lý loại sản phẩm</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 text-left">
        <a href="{{ route('admin.product-types.create') }}" 
           class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
            + Thêm loại sản phẩm
        </a>
    </div>

    <div class="bg-white p-4 rounded shadow border overflow-x-auto">
        <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Tên loại sản phẩm</th>
                    <th class="border px-4 py-2">Cần lọc loại da</th>
                    <th class="border px-4 py-2">Số sản phẩm</th>
                    <th class="border px-4 py-2">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productTypes as $productType)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2 text-center">{{ $productType->id }}</td>
                    <td class="border px-4 py-2">{{ $productType->name }}</td>
                    <td class="border px-4 py-2 text-center">
                        @if($productType->requires_skin_type_filter)
                            <span class="text-green-600 font-semibold">Có</span>
                        @else
                            <span class="text-red-600 font-semibold">Không</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2 text-center">
                        {{ \App\Models\Product::where('product_type', $productType->name)->count() }}
                    </td>
                    <td class="border px-4 py-2">
                        <div class="flex justify-center gap-2 items-center">
                            <a href="{{ route('admin.product-types.edit', $productType->id) }}" 
                               class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                Sửa
                            </a>
                            <form method="POST" action="{{ route('admin.product-types.destroy', $productType->id) }}" 
                                  onsubmit="return confirm('Bạn có chắc muốn xoá?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 min-h-[28px] bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-xs font-medium whitespace-nowrap">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border px-4 py-2 text-center text-gray-500">Chưa có loại sản phẩm nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

