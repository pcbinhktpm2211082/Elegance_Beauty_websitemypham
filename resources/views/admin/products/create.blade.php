@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold mb-2">Thêm sản phẩm mới</h1>
        <p class="text-gray-600">Tạo sản phẩm mới cho cửa hàng</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Lỗi nhập liệu:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow border space-y-6">
        @csrf

        <!-- Thông tin cơ bản -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin cơ bản</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Tên sản phẩm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm:</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('name') }}" required>
                    <p class="text-xs text-gray-500 mt-1">Tên đầy đủ của sản phẩm để hiển thị cho khách hàng.</p>
                </div>

                {{-- Giá --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá mặc định (₫):</label>
                    <input type="number" name="price" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('price') }}" required>
                    <p class="text-xs text-gray-500 mt-1">Giá này sẽ áp dụng nếu sản phẩm không có biến thể.</p>
                </div>

                {{-- Số lượng --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Số lượng:</label>
                    <input type="number" name="quantity" id="quantity" min="0" value="{{ old('quantity') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                {{-- Danh mục --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục:</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Sản phẩm nổi bật --}}
            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_featured" value="1" 
                        {{ old('is_featured') ? 'checked' : '' }} 
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Sản phẩm nổi bật</span>
                </label>
            </div>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Mô tả sản phẩm</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả chi tiết:</label>
                <textarea id="description" name="description" rows="8" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Mô tả chi tiết về công dụng, thành phần, cách dùng...</p>
            </div>
        </div>

        <!-- Ảnh chi tiết sản phẩm -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Ảnh sản phẩm</h3>
            
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Ảnh chi tiết sản phẩm (chọn nhiều)</label>
                <input type="file" name="images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Ảnh đầu tiên sẽ được sử dụng làm ảnh bìa sản phẩm. Các ảnh phụ để hiển thị trong thư viện sản phẩm.</p>
            </div>
        </div>



        <!-- Biến thể sản phẩm -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Biến thể sản phẩm</h3>
            
            <div id="variants-wrapper"></div>

            <template id="variant-template">
                <div class="variant-item mb-4 p-4 border border-gray-300 rounded bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên biến thể</label>
                            <input type="text" name="variants[0][variant_name]" placeholder="VD: Đỏ - 50ml" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá</label>
                            <input type="number" name="variants[0][price]" placeholder="Giá" min="0" step="0.01" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" name="variants[0][quantity]" placeholder="SL" min="0" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh biến thể</label>
                            <input type="file" name="variants[0][image]" accept="image/*" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="button" class="remove-variant-btn bg-red-100 text-red-700 border border-red-300 px-3 py-1 rounded hover:bg-red-200 transition text-sm font-medium">
                            ❌ Xóa biến thể
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" id="add-variant-btn" class="inline-block px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded hover:bg-green-200 transition text-sm font-medium">
                ➕ Thêm biến thể
            </button>
        </div>

        <!-- Nút lưu -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                ← Quay lại danh sách
            </a>
            <button type="submit" class="inline-block px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded hover:bg-green-200 transition text-sm font-semibold">
                💾 Lưu sản phẩm
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const variantsWrapper = document.getElementById('variants-wrapper');
    const variantTemplate = document.getElementById('variant-template').content;
    const addBtn = document.getElementById('add-variant-btn');
    let variantIndex = 0;

    addBtn.addEventListener('click', function () {
        const clone = document.importNode(variantTemplate, true);
        clone.querySelectorAll('input').forEach(input => {
            const name = input.getAttribute('name').replace('[0]', `[${variantIndex}]`);
            input.setAttribute('name', name);
            input.value = '';
        });
        variantsWrapper.appendChild(clone);
        variantIndex++;
    });

    variantsWrapper.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant-btn')) {
            e.target.closest('.variant-item').remove();
        }
    });
});
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#description'), {
    height: '400px'
}).catch(error => {
    console.error(error);
});
</script>
@endsection
