<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                                    class="relative inline-flex items-center justify-center h-11 w-11 rounded-full border border-gray-200 bg-white text-gray-500 hover:text-indigo-600 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200">
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
                                        <button id="mark-all-read" type="button" class="text-xs text-indigo-500 hover:text-indigo-700">
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
                                                            · <span class="text-indigo-500 font-medium">Chưa đọc</span>
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
                                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:border-indigo-300 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-200">
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
                            const badges = document.querySelectorAll('#notification-list span.text-indigo-500.font-medium');
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
