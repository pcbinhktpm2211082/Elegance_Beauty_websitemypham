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
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Danh mục:</label>
                        <button type="button" onclick="openCategoryModal()" 
                                class="text-xs text-gray-600 hover:text-gray-800 underline">
                            Quản lý danh mục
                        </button>
                    </div>
                    <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Loại sản phẩm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại sản phẩm:</label>
                    <select name="product_type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn loại sản phẩm (tùy chọn) --</option>
                        @foreach($productTypes as $productType)
                            <option value="{{ $productType->name }}" {{ old('product_type') == $productType->name ? 'selected' : '' }}>
                                {{ $productType->name }}
                                @if(!$productType->requires_skin_type_filter)
                                    (Bỏ qua lọc loại da)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Chọn loại sản phẩm để hệ thống gợi ý chính xác hơn. Một số loại như Lip Balm, Body Lotion, Makeup sẽ bỏ qua bộ lọc loại da.</p>
                </div>
            </div>

            {{-- Phân loại sản phẩm --}}
            <div class="mt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-900">Phân loại sản phẩm:</h4>
                    <button type="button" onclick="openClassificationModal()" 
                            class="text-xs text-gray-600 hover:text-gray-800 underline">
                        Quản lý nhãn phân loại
                    </button>
                </div>
                
                {{-- Loại da --}}
                <div class="mb-4" id="skin-types-container">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại da:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; max-width: 100%;" id="skin-types-list">
                        @foreach ($skinTypes as $skinType)
                            <label class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 {{ in_array($skinType->id, old('classifications', [])) ? 'bg-blue-50 border-blue-500' : '' }}" style="height: 38px; box-sizing: border-box; flex-shrink: 0;">
                                <input type="checkbox" name="classifications[]" value="{{ $skinType->id }}" 
                                    {{ in_array($skinType->id, old('classifications', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="flex-shrink: 0;">
                                <span class="ml-2 text-sm text-gray-700 whitespace-nowrap" style="flex-shrink: 0;">{{ $skinType->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Các vấn đề da --}}
                <div id="skin-concerns-container">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Các vấn đề da:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; max-width: 100%;" id="skin-concerns-list">
                        @foreach ($skinConcerns as $skinConcern)
                            <label class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 {{ in_array($skinConcern->id, old('classifications', [])) ? 'bg-blue-50 border-blue-500' : '' }}" style="height: 38px; box-sizing: border-box; flex-shrink: 0;">
                                <input type="checkbox" name="classifications[]" value="{{ $skinConcern->id }}" 
                                    {{ in_array($skinConcern->id, old('classifications', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="flex-shrink: 0;">
                                <span class="ml-2 text-sm text-gray-700 whitespace-nowrap" style="flex-shrink: 0;">{{ $skinConcern->name }}</span>
                            </label>
                        @endforeach
                    </div>
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
                <br>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }} 
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Sản phẩm đang hoạt động</span>
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
                    <div class="mt-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="variants[0][is_active]" value="1" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Biến thể đang hoạt động</span>
                        </label>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="button" class="remove-variant-btn bg-gray-100 text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-200 transition text-sm font-medium">
                            Xóa biến thể
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" id="add-variant-btn" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-medium">
                + Thêm biến thể
            </button>
        </div>

        <!-- Nút lưu -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Quay lại danh sách
            </a>
            <button type="submit" class="inline-block px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                Lưu sản phẩm
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

<!-- Modal quản lý danh mục -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[9999] flex items-center justify-center" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl border-2 border-gray-200" style="z-index: 10000;">
        <h2 id="categoryModalTitle" class="text-xl font-bold mb-4 text-gray-900">Thêm danh mục</h2>
        
        <form id="categoryForm" method="POST">
            @csrf
            <div id="categoryMethodField"></div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Tên danh mục:</label>
                <input type="text" name="name" id="categoryName" required
                       class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCategoryModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition font-medium border border-gray-400">
                    Hủy
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal quản lý nhãn phân loại -->
<div id="classificationModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[9999] flex items-center justify-center" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl border-2 border-gray-200" style="z-index: 10000;">
        <h2 id="classificationModalTitle" class="text-xl font-bold mb-4 text-gray-900">Thêm nhãn phân loại</h2>
        
        <form id="classificationForm" method="POST">
            @csrf
            <div id="classificationMethodField"></div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Tên nhãn:</label>
                <input type="text" name="name" id="classificationName" required
                       class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-800 mb-2">Loại:</label>
                <select name="type" id="classificationType" required
                        class="w-full border-2 border-gray-300 rounded px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="skin_type">Loại da</option>
                    <option value="skin_concern">Các vấn đề da</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeClassificationModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition font-medium border border-gray-400">
                    Hủy
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded hover:bg-gray-200 transition text-sm font-semibold">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Category Management
function openCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Thêm danh mục';
    document.getElementById('categoryForm').action = '{{ route("admin.categories.store") }}';
    document.getElementById('categoryMethodField').innerHTML = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

// Category Form Submit
document.getElementById('categoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = this.action;
    const method = this.querySelector('input[name="_method"]')?.value || 'POST';
    
    formData.append('_token', '{{ csrf_token() }}');
    if (method !== 'POST') {
        formData.append('_method', method);
    }
    
    try {
        const response = await fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Thêm danh mục mới vào dropdown
            const select = document.getElementById('category_id');
            const option = document.createElement('option');
            option.value = data.category.id;
            option.textContent = data.category.name;
            option.selected = true;
            select.appendChild(option);
            
            // Đóng modal
            closeCategoryModal();
            
            // Hiển thị thông báo
            alert(data.message || 'Thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm danh mục');
    }
});

// Classification Management
function openClassificationModal() {
    document.getElementById('classificationModalTitle').textContent = 'Thêm nhãn phân loại';
    document.getElementById('classificationForm').action = '{{ route("admin.product-classifications.store") }}';
    document.getElementById('classificationMethodField').innerHTML = '';
    document.getElementById('classificationName').value = '';
    document.getElementById('classificationType').value = 'skin_type';
    document.getElementById('classificationModal').classList.remove('hidden');
}

function closeClassificationModal() {
    document.getElementById('classificationModal').classList.add('hidden');
}

// Classification Form Submit
document.getElementById('classificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = this.action;
    
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('ajax', '1');
    
    try {
        const response = await fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Thêm nhãn mới vào danh sách checkbox
            const classification = data.classification;
            const type = classification.type;
            
            // Tìm container phù hợp
            let container;
            if (type === 'skin_type') {
                container = document.getElementById('skin-types-list');
            } else {
                container = document.getElementById('skin-concerns-list');
            }
            
            if (container) {
                const label = document.createElement('label');
                label.className = 'inline-flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50';
                label.style.cssText = 'height: 38px; box-sizing: border-box; flex-shrink: 0;';
                label.innerHTML = `
                    <input type="checkbox" name="classifications[]" value="${classification.id}" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="flex-shrink: 0;">
                    <span class="ml-2 text-sm text-gray-700 whitespace-nowrap" style="flex-shrink: 0;">${classification.name}</span>
                `;
                container.appendChild(label);
            }
            
            // Đóng modal
            closeClassificationModal();
            
            // Hiển thị thông báo
            alert(data.message || 'Thành công!');
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm nhãn phân loại');
    }
});

// Đóng modal khi click outside
document.getElementById('categoryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCategoryModal();
    }
});

document.getElementById('classificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeClassificationModal();
    }
});
</script>
@endsection
