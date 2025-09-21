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
                    Thống kê – báo cáo doanh thu
                </a>
                <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý danh mục
                </a>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý Sản phẩm
                </a>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý Người dùng
                </a>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý Đơn hàng
                </a>
                <a href="{{ route('admin.supports.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý hỗ trợ khách hàng
                </a>
                <a href="{{ route('admin.vouchers.index') }}" class="text-sm font-medium text-gray-700 hover:text-white hover:bg-blue-600 px-3 py-2 rounded transition duration-300">
                    Quản lý mã giảm giá
                </a>

            </div>

            <!-- Nút đăng xuất bên phải -->
            <div class="flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                        class="text-sm font-medium text-gray-700 hover:text-white hover:bg-red-600 px-3 py-2 rounded transition duration-300">
                        🚪 Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
