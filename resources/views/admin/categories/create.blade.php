@extends('layouts.app')

@section('content')
    <h1 class="text-xl font-bold text-center mb-4">Thêm danh mục</h1>

    <div class="bg-white p-6 rounded shadow border max-w-xl mx-auto">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold mb-1">Tên danh mục</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring focus:border-blue-300" required>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.categories.index') }}" class="inline-block px-4 py-2 bg-gray-200 text-gray-800 border border-gray-300 rounded hover:bg-gray-300 transition text-sm font-semibold mr-2">🔙 Quay lại</a>
                <button type="submit" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">💾 Thêm mới</button>
            </div>
        </form>
    </div>
@endsection
