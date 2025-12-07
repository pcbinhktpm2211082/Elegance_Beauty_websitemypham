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

        <!-- Bộ lọc nằm ngang ở trên -->
        <div class="products-filters-horizontal" style="background:#fff; border:1px solid #f1e6d3; border-radius:15px; padding:24px; margin-bottom:30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);">
            <form id="filtersForm" method="GET" action="{{ route('products.index') }}" class="filters-form-horizontal">
                <input type="hidden" name="search" value="{{ request('search') }}" />

                <!-- Hàng trên: Danh mục, Loại da, Tình trạng da, Giá -->
                <div style="display:flex; gap:20px; align-items:flex-end; flex-wrap:nowrap; overflow-x:auto; margin-bottom:20px; justify-content:space-between;">
                    <!-- Danh mục -->
                    <div class="filter-group" style="flex:1; min-width:160px; flex-shrink:0;">
                        <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px; color:#4a4a4a; font-family:'Roboto', sans-serif;">Danh mục</label>
                        <select name="category" style="width:100%; padding:10px 12px; border:1px solid #f1e6d3; border-radius:8px; font-size:14px; background:#fff; color:#333; font-family:'Roboto', sans-serif; transition:all 0.3s ease; cursor:pointer;">
                            <option value="">-- Tất cả --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Loại da -->
                    <div class="filter-group" style="flex:1; min-width:160px; flex-shrink:0;">
                        <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px; color:#4a4a4a; font-family:'Roboto', sans-serif;">Loại da</label>
                        <select name="skin_type" style="width:100%; padding:10px 12px; border:1px solid #f1e6d3; border-radius:8px; font-size:14px; background:#fff; color:#333; font-family:'Roboto', sans-serif; transition:all 0.3s ease; cursor:pointer;">
                            <option value="">-- Tất cả --</option>
                            @foreach($skinTypes as $skinType)
                                <option value="{{ $skinType->id }}" {{ request('skin_type') == $skinType->id ? 'selected' : '' }}>{{ $skinType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tình trạng da -->
                    <div class="filter-group" style="flex:1; min-width:160px; flex-shrink:0;">
                        <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px; color:#4a4a4a; font-family:'Roboto', sans-serif;">Tình trạng da</label>
                        <select name="skin_concern" style="width:100%; padding:10px 12px; border:1px solid #f1e6d3; border-radius:8px; font-size:14px; background:#fff; color:#333; font-family:'Roboto', sans-serif; transition:all 0.3s ease; cursor:pointer;">
                            <option value="">-- Tất cả --</option>
                            @foreach($skinConcerns as $skinConcern)
                                <option value="{{ $skinConcern->id }}" {{ request('skin_concern') == $skinConcern->id ? 'selected' : '' }}>{{ $skinConcern->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Giá -->
                    <div class="filter-group" style="flex:1; min-width:200px; flex-shrink:0;">
                        <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px; color:#4a4a4a; font-family:'Roboto', sans-serif;">Giá</label>
                        <div style="display:flex; gap:8px;">
                            <input type="number" name="min_price" placeholder="Từ" value="{{ request('min_price') }}" 
                                   style="flex:1; padding:10px 12px; border:1px solid #f1e6d3; border-radius:8px; font-size:14px; font-family:'Roboto', sans-serif; transition:all 0.3s ease; min-width:80px;"
                                   onfocus="this.style.borderColor='#8b5d33'; this.style.outline='none';"
                                   onblur="this.style.borderColor='#f1e6d3';">
                            <input type="number" name="max_price" placeholder="Đến" value="{{ request('max_price') }}" 
                                   style="flex:1; padding:10px 12px; border:1px solid #f1e6d3; border-radius:8px; font-size:14px; font-family:'Roboto', sans-serif; transition:all 0.3s ease; min-width:80px;"
                                   onfocus="this.style.borderColor='#8b5d33'; this.style.outline='none';"
                                   onblur="this.style.borderColor='#f1e6d3';">
                        </div>
                    </div>
                </div>

                <!-- Hàng dưới: Sắp xếp và Nút hành động -->
                <div style="display:flex; gap:16px; align-items:flex-end; flex-wrap:nowrap;">
                    <!-- Sắp xếp -->
                    <div class="filter-group" style="min-width:280px; flex-shrink:0;">
                        <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px; color:#4a4a4a; font-family:'Roboto', sans-serif;">Sắp xếp</label>
                        <div style="display:flex; gap:6px; flex-wrap:nowrap;">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => 'desc']) }}" 
                               class="sort-btn {{ request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? '#8b5d33' : '#f1e6d3' }}';">Mới nhất</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => 'asc']) }}" 
                               class="sort-btn {{ request('sort') == 'price' && request('order') == 'asc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort') == 'price' && request('order') == 'asc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort') == 'price' && request('order') == 'asc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort') == 'price' && request('order') == 'asc' ? '#8b5d33' : '#f1e6d3' }}';">Giá ↑</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => 'desc']) }}" 
                               class="sort-btn {{ request('sort') == 'price' && request('order') == 'desc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort') == 'price' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort') == 'price' && request('order') == 'desc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort') == 'price' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}';">Giá ↓</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => 'asc']) }}" 
                               class="sort-btn {{ request('sort') == 'name' && request('order') == 'asc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort') == 'name' && request('order') == 'asc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort') == 'name' && request('order') == 'asc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort') == 'name' && request('order') == 'asc' ? '#8b5d33' : '#f1e6d3' }}';">A→Z</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'rating', 'order' => 'desc']) }}" 
                               class="sort-btn {{ request('sort') == 'rating' && request('order') == 'desc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort') == 'rating' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort') == 'rating' && request('order') == 'desc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort') == 'rating' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}';">Đánh giá cao</a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sales', 'order' => 'desc']) }}" 
                               class="sort-btn {{ request('sort') == 'sales' && request('order') == 'desc' ? 'active' : '' }}"
                               style="padding:8px 12px; background:{{ request('sort') == 'sales' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}; color:{{ request('sort') == 'sales' && request('order') == 'desc' ? '#fff' : '#8b5d33' }}; border-radius:8px; text-decoration:none; font-size:12px; white-space:nowrap; font-weight:500; transition:all 0.3s ease; font-family:'Roboto', sans-serif; border:none; cursor:pointer; flex:1; text-align:center;"
                               onmouseover="if(!this.classList.contains('active')) this.style.background='#e8ddd0';"
                               onmouseout="if(!this.classList.contains('active')) this.style.background='{{ request('sort') == 'sales' && request('order') == 'desc' ? '#8b5d33' : '#f1e6d3' }}';">Bán chạy</a>
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="filter-actions" style="display:flex; gap:10px; align-items:flex-end; flex-shrink:0;">
                        <button type="submit" class="btn-primary" id="applyButton" 
                                style="padding:10px 20px; background:#8b5d33; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; font-family:'Roboto', sans-serif; transition:all 0.3s ease; white-space:nowrap; width:120px; height:40px; box-sizing:border-box;"
                                onmouseover="this.style.background='#6a4625'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(139, 93, 51, 0.3)';"
                                onmouseout="this.style.background='#8b5d33'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">Áp dụng</button>
                        <a href="{{ route('products.index') }}" class="btn-secondary" 
                           style="padding:10px 20px; background:#f1e6d3; color:#8b5d33; border:1px solid #8b5d33; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; justify-content:center; font-family:'Roboto', sans-serif; transition:all 0.3s ease; white-space:nowrap; width:120px; height:40px; text-align:center; box-sizing:border-box;"
                           onmouseover="this.style.background='#e8ddd0'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.background='#f1e6d3'; this.style.transform='translateY(0)';">Xóa lọc</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grid sản phẩm - 5 sản phẩm mỗi hàng -->
        <div class="products-grid" style="grid-template-columns: repeat(5, 1fr);">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
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

    <style>
        .products-filters-horizontal select:hover {
            border-color: #8b5d33;
        }
        .products-filters-horizontal select:focus {
            border-color: #8b5d33;
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 93, 51, 0.1);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtersForm = document.getElementById('filtersForm');
            const checkboxes = filtersForm.querySelectorAll('input[type="checkbox"]');
            const priceInputs = filtersForm.querySelectorAll('input[type="number"]');
            
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

