@php
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'dashboard'],
        ['label' => 'Danh mục', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'categories'],
        ['label' => 'Sản phẩm', 'route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'products'],
        ['label' => 'Nhãn phân loại', 'route' => 'admin.product-classifications.index', 'match' => 'admin.product-classifications.*', 'icon' => 'classifications'],
        ['label' => 'Banner', 'route' => 'admin.banners.index', 'match' => 'admin.banners.*', 'icon' => 'banners'],
        ['label' => 'Người dùng', 'route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'users'],
        ['label' => 'Đơn hàng', 'route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'orders'],
        ['label' => 'Hỗ trợ', 'route' => 'admin.supports.index', 'match' => 'admin.supports.*', 'icon' => 'support'],
        ['label' => 'Đánh giá', 'route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'icon' => 'reviews'],
        ['label' => 'Mã giảm giá', 'route' => 'admin.vouchers.index', 'match' => 'admin.vouchers.*', 'icon' => 'vouchers'],
    ];

    $icon = function ($name) {
        return match ($name) {
            'dashboard' => '<path d="M3 12l9-9 9 9M4.5 10.5v9h5v-6h5v6h5v-9"/>',
            'categories' => '<path d="M4 6h7V4H4v2zm9 0h7V4h-7v2zm0 14h7v-2h-7v2zM4 20h7v-2H4v2zM4 13h16v-2H4v2z"/>',
            'products' => '<path d="M4 7l8-4 8 4-8 4-8-4zm0 6l8 4 8-4m-16 6l8 4 8-4"/>',
            'classifications' => '<path d="M7 7h10v2H7zm0 4h10v2H7zm0 4h10v2H7zm-4-8h2v2H3zm0 4h2v2H3zm0 4h2v2H3z"/>',
            'banners' => '<path d="M4 5h16v14H4z"/><path d="M4 9l8 4 8-4"/>',
            'users' => '<path d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z"/>',
            'orders' => '<path d="M6 4h12l1 3H5l1-3zm0 5h12v11H6z"/><path d="M9 12h6m-6 4h6"/>',
            'support' => '<path d="M6 10v6a3 3 0 003 3h6a3 3 0 003-3v-6M6 10a6 6 0 1112 0M6 10H3m15 0h3"/>',
            'vouchers' => '<path d="M4 6h16v4a2 2 0 100 4v4H4v-4a2 2 0 100-4V6z"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/>',
            'reviews' => '<path d="M4 5h16v12H5l-1 3V5z"/><path d="M9 9h6m-6 4h4"/>',
            default => '<path d="M5 5h14v14H5z"/>',
        };
    };
@endphp

<nav class="h-full flex flex-col" aria-label="Left admin navigation">
    <div class="px-5 py-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-lg font-semibold">
                TA
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400">TailAdmin</p>
                <p class="text-lg font-semibold text-gray-900">Cosmetic Admin</p>
            </div>
        </a>
                    </div>

    <div class="flex-1 overflow-y-auto px-5 py-7">
        <div class="flex flex-col" style="gap: 1.5rem;">
            @foreach ($menuItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-2xl pl-4 pr-5 py-3.5 text-lg font-semibold border-l-4 transition {{ $isActive ? 'bg-indigo-50 text-indigo-700 shadow-inner border-indigo-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 border-transparent' }}">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl border {{ $isActive ? 'border-indigo-200 bg-white' : 'border-gray-200 bg-white' }}">
                        <svg class="h-5 w-5 {{ $isActive ? 'text-indigo-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                            {!! $icon($item['icon']) !!}
                        </svg>
                    </span>
                    <span class="{{ $isActive ? 'text-indigo-700' : 'text-gray-700' }}">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</nav>
