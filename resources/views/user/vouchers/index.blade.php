@extends('layouts.user')

@section('title', 'Kho voucher')

@section('content')
<main class="products-main">
    <div class="products-header">
        <h1 class="products-title">Kho voucher của bạn</h1>
        <p class="products-subtitle">Nhận và sử dụng mã giảm giá cho đơn hàng</p>
    </div>

    <div class="products-grid" style="grid-template-columns: repeat(3, 1fr);">
        @forelse($vouchers as $voucher)
            <div class="product-card">
                <div class="product-card__banner" style="width:100%; height:120px; border-radius:12px; background:linear-gradient(135deg,#f1e6d3,#fff); display:flex; align-items:center; justify-content:center; border:1px solid #f1e6d3;">
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.6rem; color:#8b5d33; font-weight:700;">{{ $voucher->code }}</div>
                </div>
                <h4 style="min-height:auto; margin-top:10px;">{{ $voucher->description ?? 'Ưu đãi hấp dẫn' }}</h4>
                <p style="margin:4px 0 10px;">{{ $voucher->discount_type === 'percent' ? $voucher->discount_value . '% OFF' : number_format($voucher->discount_value) . '₫ OFF' }}</p>
                <div style="color:#666; font-size:0.9rem; margin-bottom:8px;">@if($voucher->min_order_amount) ĐH tối thiểu: {{ number_format($voucher->min_order_amount) }}₫ @else Không yêu cầu @endif</div>
                <div class="product-actions" style="gap:8px;">
                    <button class="btn-primary" onclick="copyVoucher('{{ $voucher->code }}')">Sao chép mã</button>
                </div>
            </div>
        @empty
            <div class="no-products-found" style="grid-column: 1/-1;">
                <h3>Chưa có voucher</h3>
                <p>Hãy quay lại sau để nhận ưu đãi mới.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">{{ $vouchers->links() }}</div>
</main>

<script>
function copyVoucher(code){
    navigator.clipboard.writeText(code).then(()=>{
        alert('Đã sao chép mã: '+code);
    });
}
</script>
@endsection





