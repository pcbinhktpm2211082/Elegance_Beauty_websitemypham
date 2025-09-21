@extends('layouts.app')

@section('content')
<style>
    .image-container {
        position: relative;
        width: 100px !important;
        height: 150px !important;
        cursor: pointer;
        display: inline-block;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.75);
        opacity: 0;
        transition: opacity 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        pointer-events: auto;
    }
    
    .image-container:hover .image-overlay {
        opacity: 1;
    }
    
    .image-controls {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }
    
    .control-label {
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 1px 2px;
        border-radius: 2px;
        transition: background-color 0.2s ease;
        color: white;
        font-size: 9px;
    }
    
    .control-label:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .control-input {
        margin-right: 2px;
        width: 8px;
        height: 8px;
        pointer-events: auto;
    }
    
    .cover-badge {
        position: absolute;
        top: 1px;
        right: 1px;
        background-color: #3b82f6;
        color: white;
        font-size: 7px;
        padding: 1px 2px;
        border-radius: 50%;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        z-index: 10;
        line-height: 1;
    }
    
    .images-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: flex-start;
    }
</style>
<div class="max-w-7xl mx-auto py-6">
    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold mb-2">Sửa sản phẩm</h1>
        <p class="text-gray-600">Cập nhật thông tin sản phẩm</p>
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

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow border space-y-6">
        @csrf
        @method('PUT')

        <!-- Thông tin cơ bản -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Thông tin cơ bản</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm:</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('name', $product->name) }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá (₫):</label>
                    <input type="number" name="price" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('price', $product->price) }}" required>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Số lượng:</label>
                    <input type="number" name="quantity" id="quantity" min="0" value="{{ old('quantity', $product->quantity) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục:</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_featured" value="1" 
                        {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }} 
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
                <textarea id="description" name="description" rows="8" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        <!-- Quản lý ảnh sản phẩm -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Quản lý ảnh sản phẩm</h3>
            
            {{-- Hiển thị ảnh chi tiết hiện tại --}}
            @if($product->images->count() > 0)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Ảnh hiện tại:</label>
                <div class="images-grid">
                    @foreach($product->images as $image)
                        <div class="image-container">
                            <!-- Hình chữ nhật dọc 3x4 (72px x 96px) -->
                            <div class="w-full h-full border border-gray-300 rounded overflow-hidden bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $image->image_path) }}?v={{ $product->updated_at->timestamp }}" 
                                     alt="Ảnh sản phẩm" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-500 text-xs" style="display: none;">
                                    Ảnh không tìm thấy
                                </div>
                            </div>
                            
                            <!-- Overlay cho các tùy chọn -->
                            <div class="image-overlay">
                                <div class="image-controls">
                                    <label class="control-label">
                                        <input type="radio" name="cover_image_id" value="{{ $image->id }}" 
                                               {{ $image->is_cover ? 'checked' : '' }} 
                                               class="control-input">
                                        <span>📷</span>
                                    </label>
                                    
                                    <label class="control-label">
                                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" 
                                               class="control-input">
                                        <span>🗑️</span>
                                    </label>
                                </div>
                            </div>
                            
                            {{-- Badge ảnh bìa --}}
                            @if($image->is_cover)
                            <div class="cover-badge">
                                ⭐
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-gray-700 mb-2"><strong>Hướng dẫn sử dụng:</strong></p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>🖱️ <strong>Hover vào ảnh</strong> để hiển thị các tùy chọn quản lý</li>
                        <li>📷 <strong>Chọn ảnh bìa:</strong> Click vào icon "📷" (radio button)</li>
                        <li>🗑️ <strong>Xóa ảnh cụ thể:</strong> Tick vào icon "🗑️" (checkbox)</li>
                        <li>⭐ <strong>Badge "⭐"</strong> hiển thị ảnh đang được chọn làm ảnh bìa</li>

                        <li>💡 <strong>Lưu ý:</strong> Ảnh được chọn xóa sẽ hiển thị mờ và đen trắng</li>
                    </ul>
                </div>
            </div>
            @endif

            {{-- Thay thế ảnh mới --}}
            <div class="mt-4">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Thay thế toàn bộ ảnh sản phẩm (chọn nhiều)</label>
                <input type="file" name="images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded">
                    <p class="text-sm text-amber-800 mb-2"><strong>⚠️ Lưu ý quan trọng:</strong></p>
                    <ul class="text-xs text-amber-700 space-y-1">
                        <li>• Khi chọn ảnh mới, <strong>TẤT CẢ ảnh cũ sẽ bị XÓA HOÀN TOÀN</strong></li>
                        <li>• Ảnh đầu tiên trong danh sách mới sẽ tự động được làm ảnh bìa</li>
                        <li>• Nếu chỉ muốn xóa/thay đổi ảnh cụ thể, hãy sử dụng các tùy chọn ở trên</li>
                    </ul>
                </div>
            </div>
        </div>



        <!-- Quản lý biến thể sản phẩm -->
        <div class="bg-gray-50 p-4 rounded border">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Biến thể sản phẩm</h3>
            
            <div id="variants-wrapper">
                @foreach(old('variants', $product->variants->toArray()) as $index => $variant)
                <div class="variant-item mb-4 p-4 border border-gray-300 rounded bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên biến thể</label>
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                            <input type="text" name="variants[{{ $index }}][variant_name]" placeholder="VD: Màu đỏ - Size M" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old("variants.$index.variant_name", $variant['variant_name']) }}" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá</label>
                            <input type="number" name="variants[{{ $index }}][price]" placeholder="Giá (có thể để trống)" min="0" step="0.01" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old("variants.$index.price", $variant['price']) }}">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" name="variants[{{ $index }}][quantity]" placeholder="Số lượng" min="0" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ old("variants.$index.quantity", $variant['quantity']) }}" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh biến thể</label>
                            <input type="file" name="variants[{{ $index }}][image]" accept="image/*" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    {{-- Hiển thị ảnh hiện tại của biến thể --}}
                    @if(isset($variant['image']) && $variant['image'])
                    <div class="mt-3 p-3 bg-gray-50 rounded border">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh hiện tại:</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $variant['image']) }}" 
                                 alt="Ảnh biến thể" 
                                 class="w-20 h-20 object-cover rounded border">
                            <label class="flex items-center text-sm text-red-600">
                                <input type="checkbox" name="variants[{{ $index }}][delete_image]" value="1" class="mr-2">
                                Xóa ảnh này
                            </label>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-3 text-right">
                        <button type="button" class="remove-variant-btn bg-red-100 text-red-700 border border-red-300 px-3 py-1 rounded hover:bg-red-200 transition text-sm font-medium">
                            ❌ Xóa biến thể
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            
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
                💾 Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const variantsWrapper = document.getElementById('variants-wrapper');
        const addBtn = document.getElementById('add-variant-btn');

        // Đếm số biến thể hiện tại
        let variantIndex = variantsWrapper.querySelectorAll('.variant-item').length;

        // Template clone khi thêm biến thể mới
        function createVariantItem(index) {
            const div = document.createElement('div');
            div.classList.add('variant-item', 'mb-4', 'p-4', 'border', 'border-gray-300', 'rounded', 'bg-white');
            div.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên biến thể</label>
                        <input type="text" name="variants[${index}][variant_name]" placeholder="VD: Màu đỏ - Size M" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá</label>
                        <input type="number" name="variants[${index}][price]" placeholder="Giá (có thể để trống)" min="0" step="0.01" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                        <input type="number" name="variants[${index}][quantity]" placeholder="Số lượng" min="0" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh biến thể</label>
                        <input type="file" name="variants[${index}][image]" accept="image/*" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <button type="button" class="remove-variant-btn bg-red-100 text-red-700 border border-red-300 px-3 py-1 rounded hover:bg-red-200 transition text-sm font-medium">
                        ❌ Xóa biến thể
                    </button>
                </div>
            `;
            return div;
        }

        addBtn.addEventListener('click', function () {
            variantsWrapper.appendChild(createVariantItem(variantIndex));
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
  ClassicEditor
    .create(document.querySelector('#description'), {
        height: '400px'
    })
    .catch(error => {
      console.error(error);
    });

    // Cải thiện UX cho việc quản lý ảnh
    document.addEventListener('DOMContentLoaded', function() {
        // Hiển thị thông báo khi chọn xóa ảnh
        const deleteCheckboxes = document.querySelectorAll('input[name="delete_images[]"]');
        deleteCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const imageContainer = this.closest('.image-container');
                if (this.checked) {
                    imageContainer.style.opacity = '0.5';
                    imageContainer.style.filter = 'grayscale(100%)';
                } else {
                    imageContainer.style.opacity = '1';
                    imageContainer.style.filter = 'none';
                }
            });
        });

        // Xử lý khi chọn ảnh bìa
        const coverRadios = document.querySelectorAll('input[name="cover_image_id"]');
        coverRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Ẩn tất cả badge
                document.querySelectorAll('.cover-badge').forEach(badge => {
                    badge.style.display = 'none';
                });
                
                // Hiển thị badge cho ảnh được chọn
                const imageContainer = this.closest('.image-container');
                const badge = imageContainer.querySelector('.cover-badge');
                if (badge) {
                    badge.style.display = 'block';
                } else {
                    // Tạo badge mới nếu chưa có
                    const newBadge = document.createElement('div');
                    newBadge.className = 'cover-badge';
                    newBadge.innerHTML = '⭐';
                    imageContainer.appendChild(newBadge);
                }
            });
        });

        // Cảnh báo khi chọn ảnh mới
        const newImagesInput = document.querySelector('input[name="images[]"]');
        if (newImagesInput) {
            newImagesInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const existingImages = document.querySelectorAll('[name="cover_image_id"]').length;
                    if (existingImages > 0) {
                        if (!confirm(`Bạn đang chọn ${this.files.length} ảnh mới. Điều này sẽ XÓA TẤT CẢ ${existingImages} ảnh hiện tại. Bạn có chắc chắn muốn tiếp tục?`)) {
                            this.value = '';
                            return;
                        }
                    }
                }
            });
        }

        // Debug: Log để kiểm tra
        console.log('Image management script loaded');
        console.log('Found', deleteCheckboxes.length, 'delete checkboxes');
        console.log('Found', coverRadios.length, 'cover radio buttons');
    });
</script>
@endsection
