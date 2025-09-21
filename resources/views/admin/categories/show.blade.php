@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-bold text-center mb-4">Chi tiết danh mục</h1>

    <div class="bg-white p-6 rounded shadow border max-w-xl mx-auto text-sm">
        <div class="mb-4">
            <strong class="block font-semibold text-gray-700">ID:</strong>
            <p>{{ $category->id }}</p>
        </div>
        <div class="mb-4">
            <strong class="block font-semibold text-gray-700">Tên danh mục:</strong>
            <p>{{ $category->name }}</p>
        </div>
        <div class="mb-4">
            <strong class="block font-semibold text-gray-700">Ngày tạo:</strong>
            <p>{{ $category->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="mb-4">
            <strong class="block font-semibold text-gray-700">Ngày cập nhật:</strong>
            <p>{{ $category->updated_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="mt-6 text-right">
            <a href="{{ route('admin.categories.index') }}" class="inline-block px-4 py-2 bg-gray-200 text-gray-800 border border-gray-300 rounded hover:bg-gray-300 transition text-sm font-semibold">🔙 Quay lại danh sách</a>
        </div>
    </div>
@endsection
