<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Elegance Beauty') - @yield('subtitle', '')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            html, body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                overflow-x: hidden;
            }
            .auth-container {
                min-height: 100vh;
                width: 100%;
                background: linear-gradient(135deg, #f9f5f0 0%, #ffffff 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                margin: 0 auto;
                position: relative;
            }
            .auth-card {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(139, 93, 51, 0.15);
                padding: 32px;
                width: 100%;
                max-width: 380px;
                margin: 0 auto;
            }
            .auth-logo {
                text-align: center;
                margin-bottom: 24px;
            }
            .auth-logo h1 {
                font-family: 'Playfair Display', serif;
                font-size: 2rem;
                color: #8b5d33;
                margin: 0;
                font-weight: 600;
            }
            .auth-logo p {
                color: #666;
                margin-top: 6px;
                font-size: 0.9rem;
            }
            .auth-form .form-group {
                margin-bottom: 16px;
            }
            .auth-form label {
                display: block;
                margin-bottom: 6px;
                color: #4a4a4a;
                font-weight: 500;
                font-size: 0.9rem;
            }
            .auth-form input[type="text"],
            .auth-form input[type="email"],
            .auth-form input[type="password"] {
                width: 100%;
                padding: 10px 14px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                font-family: 'Roboto', sans-serif;
            }
            .auth-form input:focus {
                outline: none;
                border-color: #8b5d33;
                box-shadow: 0 0 0 3px rgba(139, 93, 51, 0.1);
            }
            .auth-form .checkbox-group {
                display: flex;
                align-items: center;
                margin-bottom: 16px;
            }
            .auth-form .checkbox-group input[type="checkbox"] {
                width: 16px;
                height: 16px;
                margin-right: 8px;
                accent-color: #8b5d33;
                cursor: pointer;
            }
            .auth-form .checkbox-group label {
                margin: 0;
                cursor: pointer;
                color: #4a4a4a;
                font-weight: 400;
                font-size: 0.9rem;
            }
            .auth-form .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
                flex-wrap: wrap;
                gap: 10px;
            }
            .auth-form .btn-primary {
                background-color: #8b5d33;
                color: white;
                padding: 10px 24px;
                border: none;
                border-radius: 8px;
                font-size: 0.95rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Roboto', sans-serif;
            }
            .auth-form .btn-primary:hover {
                background-color: #6a4625;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(139, 93, 51, 0.3);
            }
            .auth-form .forgot-link {
                color: #8b5d33;
                text-decoration: none;
                font-size: 0.85rem;
                transition: color 0.3s ease;
            }
            .auth-form .forgot-link:hover {
                color: #6a4625;
                text-decoration: underline;
            }
            .auth-divider {
                text-align: center;
                margin: 20px 0;
                position: relative;
            }
            .auth-divider::before {
                content: '';
                position: absolute;
                left: 0;
                top: 50%;
                width: 100%;
                height: 1px;
                background: #e0e0e0;
            }
            .auth-divider span {
                background: #ffffff;
                padding: 0 15px;
                color: #666;
                position: relative;
            }
            .auth-social {
                width: 100%;
                padding: 10px 18px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                background: white;
                color: #4a4a4a;
                font-size: 0.9rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                text-decoration: none;
                font-family: 'Roboto', sans-serif;
            }
            .auth-social:hover {
                border-color: #8b5d33;
                background: #f9f5f0;
                color: #8b5d33;
            }
            .auth-footer {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #e0e0e0;
            }
            .auth-footer a {
                color: #8b5d33;
                text-decoration: none;
                font-weight: 500;
                font-size: 0.9rem;
                transition: color 0.3s ease;
            }
            .auth-footer a:hover {
                color: #6a4625;
                text-decoration: underline;
            }
            .auth-footer span {
                font-size: 0.9rem;
            }
            .error-message {
                color: #dc3545;
                font-size: 0.8rem;
                margin-top: 4px;
            }
            @media (max-width: 640px) {
                .auth-card {
                    padding: 24px 18px;
                    max-width: 100%;
                }
                .auth-logo h1 {
                    font-size: 1.75rem;
                }
                .auth-form .form-actions {
                    flex-direction: column;
                    align-items: stretch;
                }
                .auth-form .btn-primary {
                    width: 100%;
                }
            }
        </style>
        @stack('styles')
    </head>
    <body style="font-family: 'Roboto', sans-serif; background: #f9f5f0; margin: 0; padding: 0; width: 100%; overflow-x: hidden;">
        <div class="auth-container" style="display: flex !important; align-items: center !important; justify-content: center !important; width: 100% !important; margin: 0 auto !important;">
            <div class="auth-card" style="margin: 0 auto !important;">
                <div class="auth-logo">
                    <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">
                        <h1>Elegance Beauty</h1>
                    </a>
                    <p id="auth-subtitle">Chào mừng bạn trở lại</p>
                </div>
                <script>
                    // Set subtitle based on current route
                    document.addEventListener('DOMContentLoaded', function() {
                        const path = window.location.pathname;
                        const subtitleEl = document.getElementById('auth-subtitle');
                        if (path.includes('register')) {
                            subtitleEl.textContent = 'Tạo tài khoản mới';
                            document.title = 'Đăng ký - Elegance Beauty';
                        } else if (path.includes('login')) {
                            subtitleEl.textContent = 'Chào mừng bạn trở lại';
                            document.title = 'Đăng nhập - Elegance Beauty';
                        }
                    });
                </script>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
