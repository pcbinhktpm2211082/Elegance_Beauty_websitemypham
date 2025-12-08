<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Admin Colors -->
        <style>
            /* Override indigo colors with dark gray theme (#374151) */
            .bg-indigo-600, .bg-indigo-500 { background-color: #374151 !important; }
            .bg-indigo-50 { background-color: #f3f4f6 !important; }
            .bg-indigo-100 { background-color: #e5e7eb !important; }
            .text-indigo-400 { color: #9ca3af !important; }
            .text-indigo-500, .text-indigo-600 { color: #374151 !important; }
            .text-indigo-700 { color: #1f2937 !important; }
            .text-indigo-800 { color: #111827 !important; }
            .border-indigo-200 { border-color: #d1d5db !important; }
            .border-indigo-300 { border-color: #9ca3af !important; }
            .border-indigo-500 { border-color: #374151 !important; }
            .hover\:text-indigo-600:hover, .hover\:text-indigo-700:hover { color: #1f2937 !important; }
            .hover\:border-indigo-300:hover { border-color: #9ca3af !important; }
            .hover\:bg-indigo-200:hover { background-color: #e5e7eb !important; }
            .focus\:ring-indigo-200:focus, .focus\:ring-indigo-500:focus { --tw-ring-color: rgba(55, 65, 81, 0.5) !important; }
            
            /* Override blue colors with dark gray theme */
            .bg-blue-50 { background-color: #f3f4f6 !important; }
            .bg-blue-100 { background-color: #e5e7eb !important; }
            .bg-blue-200 { background-color: #d1d5db !important; }
            .text-blue-600, .text-blue-700, .text-blue-800 { color: #374151 !important; }
            .border-blue-300, .border-blue-500 { border-color: #374151 !important; }
            .hover\:bg-blue-200:hover { background-color: #d1d5db !important; }
            .hover\:text-blue-800:hover { color: #1f2937 !important; }
            .focus\:ring-blue-500:focus { --tw-ring-color: rgba(55, 65, 81, 0.5) !important; }
            
            /* Body font */
            body {
                font-family: 'Roboto', sans-serif;
            }
            
            /* Headings font */
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Playfair Display', serif;
            }
            
            /* Fix navigation logo text colors */
            nav .text-gray-900 {
                color: #4a4a4a !important;
            }
            
            /* Fix logo display - Only text, no box */
            .admin-logo-link,
            nav a[href*="admin.dashboard"],
            nav a[href*="dashboard"] {
                display: flex !important;
                align-items: center !important;
                text-decoration: none !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            
            /* Hide/Remove any logo box - Force remove */
            .admin-logo-box,
            nav a[href*="admin.dashboard"] > div:first-child:not(.admin-logo-text),
            nav a[href*="dashboard"] > div:first-child:not(.admin-logo-text),
            nav a[href*="admin.dashboard"] > div.h-10,
            nav a[href*="dashboard"] > div.h-10,
            nav a[href*="admin.dashboard"] > div[style*="374151"],
            nav a[href*="dashboard"] > div[style*="374151"] {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                min-width: 0 !important;
                min-height: 0 !important;
                max-width: 0 !important;
                max-height: 0 !important;
                visibility: hidden !important;
                opacity: 0 !important;
                position: absolute !important;
                left: -9999px !important;
            }
            
            /* Ensure no background boxes appear */
            nav a[href*="admin.dashboard"] > div:not(.admin-logo-text),
            nav a[href*="dashboard"] > div:not(.admin-logo-text) {
                background: none !important;
                background-color: transparent !important;
            }
            
            .admin-logo-text,
            nav a[href*="admin.dashboard"] > div.admin-logo-text,
            nav a[href*="dashboard"] > div.admin-logo-text {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 2px !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }
            
            .admin-logo-subtitle,
            nav a[href*="admin.dashboard"] .admin-logo-subtitle,
            nav a[href*="dashboard"] .admin-logo-subtitle {
                color: #9ca3af !important;
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1.2 !important;
                font-size: 10px !important;
                font-weight: 400 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3em !important;
                font-family: 'Roboto', sans-serif !important;
                white-space: nowrap !important;
                display: block !important;
            }
            
            .admin-logo-title,
            nav a[href*="admin.dashboard"] .admin-logo-title,
            nav a[href*="dashboard"] .admin-logo-title {
                color: #1f2937 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Playfair Display', 'Georgia', serif !important;
                font-size: 1.125rem !important;
                font-weight: 600 !important;
                line-height: 1.3 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                display: block !important;
            }
        </style>
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-100 overflow-x-hidden">
        <div class="min-h-screen w-screen max-w-full flex bg-slate-50 overflow-hidden">
            <!-- Left sidebar navigation -->
            <aside class="flex-shrink-0 border-r border-gray-200 bg-white shadow-lg" style="width: 260px;">
            @include('layouts.navigation')
            </aside>

            <!-- Main panel -->
            <div class="flex-1 flex flex-col min-h-screen min-w-0 overflow-hidden">
                <!-- Top bar -->
                <header class="bg-white border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4 px-4 sm:px-6 lg:px-10 py-4">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-gray-900">Bảng điều khiển</p>
                            <p class="text-sm text-gray-500">Quản lý hoạt động cửa hàng mỹ phẩm</p>
                        </div>

                        <div class="flex items-center gap-4 relative">
                            <div class="hidden sm:flex flex-col text-right">
                                <span class="text-xs text-gray-500">Xin chào,</span>
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</span>
                            </div>

                            <!-- Thông báo -->
                            <div class="relative">
                                @php
                                    $adminNotifications = \App\Models\AdminNotification::latest()->take(5)->get();
                                    // Kiểm tra toàn bộ bảng chứ không chỉ 5 bản ghi để hiển thị chấm đỏ chính xác
                                    $hasUnreadNotifications = \App\Models\AdminNotification::where('is_read', false)->exists();
                                @endphp

                                <button id="notification-button" type="button"
                                    class="relative inline-flex items-center justify-center h-11 w-11 rounded-full border border-gray-200 bg-white text-gray-500 focus:outline-none focus:ring-2"
                                    style="--hover-color: #374151; --hover-border: #9ca3af; --focus-ring: rgba(55, 65, 81, 0.2);"
                                    onmouseover="this.style.color='#374151'; this.style.borderColor='#9ca3af';"
                                    onmouseout="this.style.color='#6b7280'; this.style.borderColor='#e5e7eb';">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                                    </svg>
                                    <span id="notification-dot"
                                          style="position:absolute; top:-3px; right:-3px; width:10px; height:10px; border-radius:9999px; background-color:#ef4444; {{ $hasUnreadNotifications ? '' : 'display:none;' }}"></span>
                                </button>

                                <div id="notification-panel"
                                     class="absolute mt-3 rounded-2xl border border-gray-100 bg-white shadow-xl py-2 text-[13px] text-gray-700 leading-relaxed hidden z-20"
                                     style="width: 360px; left: 50%; transform: translateX(-50%);">
                                    <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-800">Thông báo</p>
                                        <button id="mark-all-read" type="button" class="text-xs" 
                                                style="color: #374151;"
                                                onmouseover="this.style.color='#1f2937';"
                                                onmouseout="this.style.color='#374151';">
                                            Đánh dấu đã đọc
                                        </button>
                                    </div>
                                    <div id="notification-list" class="max-h-80 overflow-y-auto">
                                        @forelse ($adminNotifications as $notification)
                                            @php
                                                $icon = match($notification->type) {
                                                    'success' => '✅',
                                                    'warning' => '⚠️',
                                                    'error' => '❌',
                                                    default => '🔔',
                                                };
                                            @endphp
                                            <div class="px-4 py-3.5 flex items-start gap-3 hover:bg-gray-50 {{ !$loop->first ? 'border-t border-gray-100' : '' }}">
                                                <span class="mt-1">
                                                    {{ $icon }}
                                                </span>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        {{ $notification->title }}
                                                    </p>
                                                    @if ($notification->message)
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ $notification->message }}
                                                        </p>
                                                    @endif
                                                    <p class="text-[11px] text-gray-400 mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                        @unless($notification->is_read)
                                                            · <span class="font-medium" style="color: #374151;">Chưa đọc</span>
                                                        @endunless
                                                    </p>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-4 py-6 text-center text-xs text-gray-500">
                                                Hiện tại không có thông báo nào.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <button id="user-menu-button" type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2"
                                style="--hover-color: #374151; --hover-border: #9ca3af; --focus-ring: rgba(55, 65, 81, 0.2);"
                                onmouseover="this.style.color='#374151'; this.style.borderColor='#9ca3af';"
                                onmouseout="this.style.color='#374151'; this.style.borderColor='#e5e7eb';">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8 8 0 1118.879 6.196 8 8 0 015.12 17.804z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Tài khoản</span>
                            </button>
                            <div id="user-menu-panel"
                                 class="absolute right-0 top-16 w-52 rounded-2xl border border-gray-100 bg-white shadow-xl py-2 text-sm text-gray-700 hidden">
                                <a href="{{ route('admin.password.edit') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-50">
                                    <span>🔒</span> Đổi mật khẩu
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-red-600 hover:bg-red-50">
                                        <span>🚪</span> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if (isset($header))
                        <div class="border-t border-gray-100 bg-gray-50">
                            <div class="px-4 sm:px-6 lg:px-10 py-4">
                                {{ $header }}
                            </div>
                        </div>
                    @endif
                </header>

            <!-- Page Content -->
                <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 py-6 min-w-0 overflow-x-auto">
                @yield('content')
            </main>
            </div>
        </div>

        <script>
            (function () {
                // Dropdown tài khoản
                const userButton = document.getElementById('user-menu-button');
                const userPanel = document.getElementById('user-menu-panel');

                // Dropdown thông báo
                const notiButton = document.getElementById('notification-button');
                const notiPanel = document.getElementById('notification-panel');
                const notiDot = document.getElementById('notification-dot');
                const markAllRead = document.getElementById('mark-all-read');

                const togglePanel = (panel) => {
                    if (!panel) return;
                    panel.classList.toggle('hidden');
                };

                const closePanel = (panel) => {
                    if (!panel) return;
                    panel.classList.add('hidden');
                };

                // Xử lý dropdown tài khoản
                if (userButton && userPanel) {
                    userButton.addEventListener('click', (event) => {
                        event.stopPropagation();
                        togglePanel(userPanel);
                        // Đóng panel thông báo nếu đang mở
                        closePanel(notiPanel);
                    });
                }

                // Xử lý dropdown thông báo
                if (notiButton && notiPanel) {
                    notiButton.addEventListener('click', (event) => {
                        event.stopPropagation();
                        togglePanel(notiPanel);
                        // Đóng panel tài khoản nếu đang mở
                        closePanel(userPanel);
                    });
                }

                // Đánh dấu đã đọc: ẩn chấm đỏ
                if (markAllRead) {
                    markAllRead.addEventListener('click', () => {
                        fetch("{{ route('admin.notifications.markAllRead') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({}),
                        }).then(() => {
                            if (notiDot) {
                                notiDot.style.display = 'none';
                            }
                            const badges = document.querySelectorAll('#notification-list span.font-medium[style*="color: #374151"]');
                            badges.forEach((badge) => badge.classList.add('hidden'));
                        }).catch(() => {
                            // Silent fail, không cần báo lỗi cho người dùng trong trường hợp này
                        });
                    });
                }

                // Hàm kiểm tra định kỳ số lượng thông báo chưa đọc để hiển thị chấm đỏ
                const refreshUnreadDot = () => {
                    if (!notiDot) return;

                    fetch("{{ route('admin.notifications.unreadCount') }}", {
                        headers: {
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.unread_count && data.unread_count > 0) {
                                notiDot.style.display = 'inline-block';
                            } else {
                                notiDot.style.display = 'none';
                            }
                        })
                        .catch(() => {
                            // Nếu lỗi thì giữ trạng thái hiện tại, không làm gì
                        });
                };

                // Gọi khi load trang và mỗi 30 giây để cập nhật
                refreshUnreadDot();
                setInterval(refreshUnreadDot, 30000);

                // Đóng khi click ra ngoài
                document.addEventListener('click', (event) => {
                    if (userPanel && !userPanel.contains(event.target) && !userButton?.contains(event.target)) {
                        closePanel(userPanel);
                    }
                    if (notiPanel && !notiPanel.contains(event.target) && !notiButton?.contains(event.target)) {
                        closePanel(notiPanel);
                    }
                });
            })();
        </script>
    </body>
</html>
