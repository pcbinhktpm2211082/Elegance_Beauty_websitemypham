@extends('layouts.user')

@section('title', 'Sản phẩm')

@section('content')
    <main class="products-main">
        <div class="products-header">
            <h1 class="products-title">
                @if(request('search'))
                    Kết quả tìm kiếm: "{{ request('search') }}"
                @elseif(request('categories') || request('category'))
                    Sản phẩm được lọc theo danh mục
                @else
                    Tất cả sản phẩm
                @endif
            </h1>
            
            <p class="products-subtitle">
                Tìm thấy {{ $products->total() }} sản phẩm
                @if(request('categories'))
                    trong danh mục: 
                    @php
                        $selectedCategories = $categories->whereIn('id', (array)request('categories'));
                    @endphp
                    {{ $selectedCategories->pluck('name')->join(', ') }}
                @elseif(request('category'))
                    @php
                        $selectedCategory = $categories->firstWhere('id', request('category'));
                    @endphp
                    @if($selectedCategory)
                        trong danh mục: {{ $selectedCategory->name }}
                    @endif
                @endif
            </p>
        </div>

        <div class="products-layout" style="display:grid; grid-template-columns: 240px 1fr; gap:20px; align-items:start;">
            <!-- Sidebar bộ lọc -->
            <aside class="products-sidebar" style="position:sticky; top:20px;">
                <form id="filtersForm" method="GET" action="{{ route('products.index') }}" class="filters-form" style="display:flex; flex-direction:column; gap:16px;">
                    <input type="hidden" name="search" value="{{ request('search') }}" />

                    <div class="filter-card" style="background:#fff; border:1px solid #f1e6d3; border-radius:12px; padding:12px;">
                        <div class="filter-title" style="font-weight:700; margin-bottom:8px;">Danh mục</div>
                        <div class="filter-list" style="display:flex; flex-direction:column; gap:6px; max-height:200px; overflow:auto;">
                            @foreach($categories as $category)
                                <label style="display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, (array)request('categories', request('category') ? [request('category')] : [])) ? 'checked' : '' }}>
                                    <span>{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p style="font-size: 11px; color: #6b7280; margin-top: 8px; font-style: italic;">Chọn danh mục và nhấn "Áp dụng" để lọc</p>
                    </div>

                    <div class="filter-card" style="background:#fff; border:1px solid #f1e6d3; border-radius:12px; padding:12px;">
                        <div class="filter-title" style="font-weight:700; margin-bottom:8px;">Giá</div>
                        <div class="filter-row" style="display:flex; gap:6px;">
                            <div style="flex:1; min-width: 0;">
                                <input type="number" name="min_price" class="form-input" placeholder="Từ" value="{{ request('min_price') }}" style="width:100%;">
                            </div>
                            <div style="flex:1; min-width: 0;">
                                <input type="number" name="max_price" class="form-input" placeholder="Đến" value="{{ request('max_price') }}" style="width:100%;">
                            </div>
                        </div>
                    </div>

                    <div class="filter-card" style="background:#fff; border:1px solid #f1e6d3; border-radius:12px; padding:12px;">
                        <div class="filter-title" style="font-weight:700; margin-bottom:8px;">Tình trạng</div>
                        <label style="display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                            <span>Còn hàng</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <input type="checkbox" name="is_featured" value="1" {{ request('is_featured') ? 'checked' : '' }}>
                            <span>Khuyến mãi/Nổi bật</span>
                        </label>
                    </div>

                    <div class="filter-actions" style="display:flex; gap:8px;">
                        <button type="submit" class="btn-primary" id="applyButton" style="flex:1;">Áp dụng</button>
                        <a href="{{ route('products.index') }}" class="btn-secondary" style="flex:1; text-decoration: none;">Xóa lọc</a>
                    </div>
                </form>

                <!-- Nhóm sắp xếp -->
                <div class="filter-card" style="background:#fff; border:1px solid #f1e6d3; border-radius:12px; padding:12px; margin-top:12px;">
                    <div class="filter-title" style="font-weight:700; margin-bottom:8px;">Sắp xếp theo</div>
                    <div class="filter-options" style="display:flex; flex-direction:column; gap:6px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => 'desc']) }}" class="filter-option {{ request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? 'active' : '' }}">Mới nhất</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => 'asc']) }}" class="filter-option {{ request('sort') == 'price' && request('order') == 'asc' ? 'active' : '' }}">Giá thấp → cao</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => 'desc']) }}" class="filter-option {{ request('sort') == 'price' && request('order') == 'desc' ? 'active' : '' }}">Giá cao → thấp</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => 'asc']) }}" class="filter-option {{ request('sort') == 'name' && request('order') == 'asc' ? 'active' : '' }}">Tên A → Z</a>
                    </div>
                </div>
            </aside>

            <!-- Grid sản phẩm - tối đa 4 sản phẩm mỗi hàng -->
            <div class="products-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>

        <!-- Phân trang -->
        @if($products->hasPages())
            <div class="pagination-container">
                {{ $products->render() }}
            </div>
        @endif

        <!-- Không có kết quả -->
        @if($products->count() == 0)
            <div class="no-products-found">
                <div class="no-products-icon">🔍</div>
                <h3>Không tìm thấy sản phẩm</h3>
                <p>Hãy thử tìm kiếm với từ khóa khác</p>
                <a href="{{ route('products.index') }}" class="btn-secondary">Xem tất cả sản phẩm</a>
            </div>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtersForm = document.getElementById('filtersForm');
            const checkboxes = filtersForm.querySelectorAll('input[type="checkbox"]');
            const priceInputs = filtersForm.querySelectorAll('input[type="number"]');
            
            // Chỉ submit khi nhấn nút "Áp dụng" - không auto submit
            // Các checkbox và input giá sẽ chỉ lưu giá trị, không tự động submit
            
            // Thêm visual feedback khi có thay đổi
            function markFormAsChanged() {
                const applyButton = filtersForm.querySelector('button[type="submit"]');
                if (applyButton) {
                    applyButton.textContent = 'Áp dụng *';
                    applyButton.classList.add('changed');
                }
            }
            
            // Đánh dấu form có thay đổi khi checkbox thay đổi
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    markFormAsChanged();
                });
            });
            
            // Đánh dấu form có thay đổi khi giá thay đổi
            priceInputs.forEach(input => {
                input.addEventListener('input', function() {
                    markFormAsChanged();
                });
            });
            
            // Reset trạng thái khi submit
            filtersForm.addEventListener('submit', function() {
                const applyButton = filtersForm.querySelector('button[type="submit"]');
                if (applyButton) {
                    applyButton.textContent = 'Áp dụng';
                    applyButton.classList.remove('changed');
                }
            });
        });
    </script>
@endsection
