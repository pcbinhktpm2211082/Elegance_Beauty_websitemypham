<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang người dùng')</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    {{-- Link CSS --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Nếu có thêm CSS riêng cho từng trang --}}
    @stack('styles')
</head>
<body>

    {{-- Header dùng chung --}}
    @include('user.partials.header')

    {{-- Nội dung riêng từng trang --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer dùng chung --}}
    @include('user.partials.footer')

    {{-- JS dùng chung --}}
    {{-- Chặn scroll ngang hoàn toàn --}}
    <script>
        // Chặn scroll ngang bằng JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Chặn scroll ngang
            function preventHorizontalScroll(e) {
                if (e.deltaX !== 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }

            // Chặn scroll ngang bằng wheel
            document.addEventListener('wheel', preventHorizontalScroll, { passive: false });
            document.addEventListener('touchmove', function(e) {
                if (e.touches.length > 1) {
                    e.preventDefault();
                }
            }, { passive: false });

            // Đảm bảo không có scroll ngang
            function preventHorizontalScrollEvent(e) {
                if (e.keyCode === 37 || e.keyCode === 39) { // Left/Right arrow keys
                    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                    }
                }
            }
            document.addEventListener('keydown', preventHorizontalScrollEvent);

            // Đặt scrollLeft = 0 liên tục
            setInterval(function() {
                if (document.documentElement.scrollLeft !== 0) {
                    document.documentElement.scrollLeft = 0;
                }
                if (document.body.scrollLeft !== 0) {
                    document.body.scrollLeft = 0;
                }
                // Kiểm tra header
                const header = document.querySelector('header');
                if (header && header.scrollLeft !== 0) {
                    header.scrollLeft = 0;
                }
                const headerTop = document.querySelector('.header-top');
                if (headerTop && headerTop.scrollLeft !== 0) {
                    headerTop.scrollLeft = 0;
                }
            }, 100);
        });
    </script>
    {{-- Nếu có JS riêng --}}
    @stack('scripts')
</body>
</html>
