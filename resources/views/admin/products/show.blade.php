@extends('layouts.app')

@section('content')
<style>
.card-container {
    max-width: 1200px;
    margin: 2rem auto;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    padding: 2rem;
}

.main-image {
    width: 400px;
    height: 400px;
    object-fit: cover;
    border-radius: 0.5rem;
    border: 1px solid #ddd;
    position: relative;
    display: block;
}
.thumbnail {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border: 2px solid transparent;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: border-color 0.3s ease;
}
.thumbnail.active {
    border-color: #f87171;
}
.variant-btn {
    border: 1px solid #ddd;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 120px;
    justify-content: center;
}
.variant-btn.selected {
    border-color: #f87171;
    background-color: #fee2e2;
    color: #b91c1c;
    font-weight: 600;
}
.variant-btn img {
    border-radius: 0.25rem;
    border: 1px solid #e5e7eb;
}
.quantity-input {
    width: 60px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    padding: 0.25rem 0.5rem;
}
.quantity-btn {
    cursor: pointer;
    background-color: #f87171;
    color: white;
    padding: 0.25rem 1rem;
    font-weight: bold;
    border-radius: 0.375rem;
    user-select: none;
}
.quantity-btn:hover {
    background-color: #ef4444;
}
.product-name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 2rem;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    letter-spacing: 0;
    line-height: 1.2;
}
/* Price styles giống user */
.product-price { margin: 10px 0 16px; display:flex; align-items:center; gap:10px; }
.product-price .current-price { color:#c0392b; font-size: 1.6rem; font-weight:700; }
.product-price .original-price { color:#888; text-decoration: line-through; }
.product-price .discount-badge { background:#e74c3c; color:#fff; padding:2px 8px; border-radius:6px; font-size:12px; font-weight:700; }

/* Meta giống user */
.product-meta { display:grid; grid-template-columns: 1fr 1fr; gap:10px 16px; margin-top: 16px; }
.product-meta .meta-item { display:flex; align-items:center; gap:8px; }
.product-meta .meta-label { color:#666; }
.product-meta .meta-value { font-weight:600; }
.meta-value.in-stock { color:#27ae60; }
.meta-value.out-of-stock { color:#e74c3c; }

/* Tabs cho mô tả/đánh giá */
.description-tabs { display:flex; gap:10px; margin-top: 24px; }
.description-tabs .tab-btn { padding:8px 14px; border:1px solid #e5e7eb; border-radius:8px; background:#fff; cursor:pointer; }
.description-tabs .tab-btn.active { background:#f7efe6; border-color:#f1e6d3; color:#8b5d33; font-weight:600; }
.tab-content { display:none; padding:16px 0; }
.tab-content.active { display:block; }
.img-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(229, 229, 229, 0.8);
    border-radius: 9999px;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.2rem;
    user-select: none;
    transition: background-color 0.3s ease;
    border: none;
    color: #000;
    width: 28px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.img-nav-btn:hover {
    background-color: rgba(160,160,160,0.9);
}
.img-nav-btn.prev { left: 0; }
.img-nav-btn.next { right: 0; }
.thumbnails-wrapper {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    margin-top: 0.5rem;
}
</style>

<div class="card-container">
    <div class="flex flex-wrap" style="column-gap:2cm;">
        {{-- Ảnh sản phẩm --}}
        <div class="relative inline-block">
            <button id="prevImageBtn" class="img-nav-btn prev">‹</button>
            @php
                $cover = $product->coverOrFirstImage;
            @endphp
            @if($cover)
                <img id="mainImage" src="{{ asset('storage/' . $cover) }}" alt="Ảnh sản phẩm" class="main-image mb-4">
            @else
                <img id="mainImage" src="{{ asset('storage/placeholder.jpg') }}" alt="Không có ảnh" class="main-image mb-4">
            @endif
            <button id="nextImageBtn" class="img-nav-btn next">›</button>

            <div class="thumbnails-wrapper">
                @foreach ($product->images as $index => $image)
                    <img src="{{ asset('storage/' . $image->image_path) }}"
                         class="thumbnail {{ $index === 0 ? 'active' : '' }}"
                         data-index="{{ $index }}">
                @endforeach
            </div>
        </div>

        {{-- Thông tin sản phẩm --}}
        <div class="flex-1 min-w-0 flex flex-col">
            <h1 class="product-name">{{ $product->name }}</h1>
            <div class="product-price" id="productPriceWrap">
                <span class="current-price" id="productPrice">{{ number_format($product->price,0,',','.') }} VNĐ</span>
            </div>

            <h3 class="text-lg font-semibold mt-4">Phân loại sản phẩm</h3>
            @if($product->variants->isNotEmpty())
            <div class="flex gap-4 flex-wrap mb-4">
                @foreach($product->variants as $variant)
                <div class="variant-btn {{ !$variant->is_active ? 'opacity-50' : '' }}"
                     data-price="{{ $variant->price }}"
                     data-variant-id="{{ $variant->id }}"
                     @if($variant->image) data-variant-image="{{ asset('storage/' . $variant->image) }}" @endif
                >
                    @if($variant->image)
                        <img src="{{ asset('storage/' . $variant->image) }}" 
                             alt="{{ $variant->variant_name }}" 
                             class="w-8 h-8 object-cover rounded mr-2 inline-block">
                    @endif
                    {{ $variant->variant_name }}
                    @if(!$variant->is_active)
                        <span class="text-xs text-red-600 ml-1">(Vô hiệu hóa)</span>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="mb-4 p-3 bg-gray-50 rounded border">
                <h4 class="text-sm font-semibold mb-2">Quản lý biến thể:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->variants as $variant)
                    <div class="flex items-center gap-2 p-2 bg-white border rounded">
                        <span class="text-sm">{{ $variant->variant_name }}</span>
                        <span class="text-xs {{ $variant->is_active ? 'text-green-600' : 'text-red-600' }}">
                            ({{ $variant->is_active ? 'Đang hoạt động' : 'Vô hiệu hóa' }})
                        </span>
                        <form action="{{ route('admin.products.variants.toggle-status', [$product->id, $variant->id]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-xs px-2 py-1 {{ $variant->is_active ? 'bg-orange-100 text-orange-800 border-orange-300 hover:bg-orange-200' : 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200' }} border rounded transition">
                                    {{ $variant->is_active ? '🚫 Vô hiệu hóa' : '✅ Kích hoạt' }}
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Số lượng (demo - disabled) --}}
            <div class="mb-6 flex items-center space-x-4 gap-4 opacity-60">
                <span class="font-semibold">Số lượng:</span>
                <button class="quantity-btn" disabled>-</button>
                <input type="number" class="quantity-input" value="1" min="1" max="100" disabled>
                <button class="quantity-btn" disabled>+</button>
            </div>

            {{-- Thông tin meta giống user --}}
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Tình trạng:</span>
                    @php
                        $totalQuantity = $product->variants->count() > 0 ? $product->variants->sum('quantity') : $product->quantity;
                    @endphp
                    <span class="meta-value {{ $totalQuantity>0 ? 'in-stock' : 'out-of-stock' }}">
                        {{ $totalQuantity>0 ? 'Còn hàng ('.$totalQuantity.')' : 'Hết hàng' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Mã sản phẩm:</span>
                    <span class="meta-value">{{ $product->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Danh mục:</span>
                    <span class="meta-value">{{ $product->category->name ?? 'Không phân loại' }}</span>
                </div>
            </div>

            {{-- Nút hành động (demo - vô hiệu hoá) --}}
            <div class="flex space-x-4 gap-4 mt-20 opacity-60">
                <button class="px-8 py-4 border border-red-400 text-red-600 rounded font-semibold flex items-center space-x-2 cursor-not-allowed" disabled aria-disabled="true" title="Demo - không khả dụng">
                    <span>Thêm vào giỏ hàng</span>
                </button>
                <button class="px-6 py-3 bg-red-600 text-white rounded font-semibold cursor-not-allowed" disabled aria-disabled="true" title="Demo - không khả dụng">
                    Mua ngay
                </button>
            </div>
        </div>
    </div>

    {{-- Mô tả / Đánh giá giống user (demo) --}}
    <div class="mt-12 max-w-4xl mx-auto text-gray-700 leading-relaxed">
        <div class="description-tabs">
            <button class="tab-btn active" onclick="showTabAdmin('desc', event)">Mô tả</button>
            <button class="tab-btn" onclick="showTabAdmin('reviews', event)">Đánh giá</button>
        </div>
        <div id="tab-desc" class="tab-content active">
            <h3 class="font-bold mb-2 text-lg">Mô tả sản phẩm</h3>
            {!! $product->description !!}
        </div>
        <div id="tab-reviews" class="tab-content">
            <h3 class="font-bold mb-2 text-lg">Đánh giá sản phẩm</h3>
            <p>Đây là bản trình diễn giao diện nên chưa có tính năng đánh giá.</p>
        </div>
    </div>
</div>

<script>
let currentVariantImages = @json($product->images->map(fn($img)=>asset('storage/'.$img->image_path)));
const mainImage = document.getElementById('mainImage');
let thumbnailsWrapper = document.querySelector('.thumbnails-wrapper');
let currentIndex = 0;

// Hàm render thumbnails
function renderThumbnails(images) {
    thumbnailsWrapper.innerHTML = '';
    images.forEach((imgSrc, idx) => {
        const img = document.createElement('img');
        img.src = imgSrc;
        img.className = 'thumbnail' + (idx===0?' active':'');
        img.dataset.index = idx;
        img.addEventListener('click', () => {
            updateMainImage(idx);
        });
        thumbnailsWrapper.appendChild(img);
    });
    // Update NodeList sau khi render
    thumbnails = thumbnailsWrapper.querySelectorAll('.thumbnail');
    currentIndex = 0; // reset index về 0
    mainImage.src = images[0]; // set ảnh đầu tiên
}

// Hàm cập nhật ảnh chính
function updateMainImage(index) {
    if(index<0) index=thumbnails.length-1;
    if(index>=thumbnails.length) index=0;
    currentIndex = index;
    thumbnails.forEach(t=>t.classList.remove('active'));
    thumbnails[currentIndex].classList.add('active');
    mainImage.src = thumbnails[currentIndex].src;
}

// Init thumbnails ban đầu
renderThumbnails(currentVariantImages);

// Prev / Next
document.getElementById('prevImageBtn').addEventListener('click', () => updateMainImage(currentIndex-1));
document.getElementById('nextImageBtn').addEventListener('click', () => updateMainImage(currentIndex+1));

// Chọn biến thể
document.querySelectorAll('.variant-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
        document.querySelectorAll('.variant-btn').forEach(b=>b.classList.remove('selected'));
        this.classList.add('selected');

        // Cập nhật giá
        const newPrice = this.dataset.price;
        if(newPrice){
            document.getElementById('productPrice').textContent='Giá: '+Number(newPrice).toLocaleString('vi-VN')+'₫';
        }

        // Cập nhật ảnh + thumbnails
        const variantImage = this.dataset.variantImage;
        if(variantImage) {
            // Nếu biến thể có ảnh riêng, hiển thị ảnh đó + ảnh sản phẩm
            const allImages = [variantImage, ...@json($product->images->map(fn($img)=>asset('storage/'.$img->image_path)))];
            currentVariantImages = allImages;
            renderThumbnails(allImages);
        } else {
            // Nếu không có ảnh riêng, chỉ hiển thị ảnh sản phẩm
            currentVariantImages = @json($product->images->map(fn($img)=>asset('storage/'.$img->image_path)));
            renderThumbnails(currentVariantImages);
        }
    });
});

// Tabs demo
function showTabAdmin(name, e){
  document.querySelectorAll('.tab-content').forEach(el=>el.classList.remove('active'));
  document.querySelectorAll('.description-tabs .tab-btn').forEach(el=>el.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  if(e) e.target.classList.add('active');
}
</script>
@endsection
