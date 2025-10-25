<nav class="bg-white border-b border-gray-200 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16 justify-between">
            <!-- Logo bên trái -->
            <div class="flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-gray-800 hover:text-blue-600 transition duration-300">
                    🛍️ MyShop Admin
                </a>
            </div>

            <!-- Menu căn giữa -->
            <div class="flex-grow flex justify-center space-x-8">
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Thống kê – báo cáo 
                </a>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                     Danh mục
                </a>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                     Sản phẩm
                </a>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Người dùng
                </a>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Đơn hàng
                </a>
                <a href="{{ route('admin.supports.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Hỗ trợ khách hàng
                </a>
                <a href="{{ route('admin.vouchers.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Mã giảm giá
                </a>

            </div>

            <!-- Dropdown admin bên phải -->
            <div class="flex-shrink-0 relative">
                <div class="relative inline-block text-left">
                    <div>
                        <button type="button" 
                            class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                            id="admin-menu-button" 
                            aria-expanded="false" 
                            aria-haspopup="true"
                            onclick="toggleAdminMenu()">
                            {{ Auth::user()->name }}
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" 
                         role="menu" 
                         aria-orientation="vertical" 
                         aria-labelledby="admin-menu-button" 
                         tabindex="-1" 
                         id="admin-menu">
                        <div class="py-1" role="none">
                            <!-- Đổi mật khẩu -->
                            <a href="{{ route('admin.password.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" 
                               role="menuitem" 
                               tabindex="-1">
                                Đổi mật khẩu
                            </a>
                            
                            <!-- Đăng xuất -->
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" 
                                    role="menuitem" 
                                    tabindex="-1">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleAdminMenu() {
    const menu = document.getElementById('admin-menu');
    const button = document.getElementById('admin-menu-button');
    
    if (menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
    } else {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    }
}

// Đóng dropdown khi click bên ngoài
document.addEventListener('click', function(event) {
    const menu = document.getElementById('admin-menu');
    const button = document.getElementById('admin-menu-button');
    
    if (!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
    }
});
</script>
