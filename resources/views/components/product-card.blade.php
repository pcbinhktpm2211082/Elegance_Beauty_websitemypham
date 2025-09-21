@props(['product'])

<div class="product-card">
    @php
        $cover = $product->coverOrFirstImage;
    @endphp

    @if ($cover)
        <img src="{{ asset('storage/' . $cover) }}" 
             alt="{{ $product->name }}">
    @else
        <img src="{{ asset('storage/placeholder.jpg') }}" 
             alt="Không có ảnh">
    @endif

    <h4>{{ $product->name }}</h4>
    <p>{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
    
    <div class="product-actions">
        <a href="{{ route('user.products.show', $product->id) }}" class="view-details">
            Xem chi tiết
        </a>
    </div>
</div>
