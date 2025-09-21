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
    {{-- Nếu có JS riêng --}}
    @stack('scripts')
</body>
</html>
