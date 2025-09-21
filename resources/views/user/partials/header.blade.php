<header>
    <div class="logo">
        <h1>Elegance Beauty</h1>
    </div>
    <nav>
        <ul>
            <li><a href="{{ url('/') }}" class="{{ Request::is('/') ? 'active' : '' }}">Trang Chủ</a></li>
            <li><a href="{{ route('products.index') }}">Sản Phẩm</a></li>
            <li><a href="{{ url('/about') }}" class="{{ Request::is('about') ? 'active' : '' }}">Về Chúng Tôi</a></li>
            <li><a href="{{ route('contact.index') }}" class="{{ Request::is('contact') ? 'active' : '' }}">Liên Hệ</a></li>
        </ul>
    </nav>
    <div class="header-icons">
        <div class="search-container">
            <a href="#" class="icon-search" id="searchToggle">
                <i class="fas fa-search"></i>
            </a>
            <div class="search-dropdown" id="searchDropdown">
                <div class="search-box">
                    <input type="text" 
                           id="headerSearch" 
                           placeholder="Tìm kiếm sản phẩm..."
                           class="search-input">
                    <button type="button" id="searchBtn" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>
        <a href="{{ route('cart.index') }}" class="icon-cart">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount">0</span>
        </a>
        <div class="profile-container">
            <a href="#" class="icon-account" id="profileToggle">
                <i class="fas fa-user"></i>
            </a>
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="{{ auth()->check() && auth()->user() && auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('images/default-avatar.png') }}" 
                             alt="Avatar" id="profileAvatar">
                        <div class="avatar-upload">
                            <label for="avatarInput" class="avatar-upload-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div class="profile-info">
                        <h3 id="profileName">{{ auth()->check() && auth()->user() ? auth()->user()->name : 'Khách' }}</h3>
                        <p id="profileEmail">{{ auth()->check() && auth()->user() ? auth()->user()->email : '' }}</p>
                    </div>
                </div>
                
                <div class="profile-menu">
                    @auth
                        <a href="{{ route('profile.show') }}" class="profile-menu-item">
                            <i class="fas fa-user-circle"></i>
                            <span>Thông tin cá nhân</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                            <i class="fas fa-edit"></i>
                            <span>Chỉnh sửa thông tin</span>
                        </a>
                        <a href="{{ route('profile.password') }}" class="profile-menu-item">
                            <i class="fas fa-key"></i>
                            <span>Đổi mật khẩu</span>
                        </a>
                        <a href="{{ route('profile.addresses') }}" class="profile-menu-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Địa chỉ giao hàng</span>
                        </a>
                        <a href="{{ route('orders.index') }}" class="profile-menu-item">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('user.vouchers.index') }}" class="profile-menu-item">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Kho voucher</span>
                        </a>
                        <div class="profile-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="profile-menu-item logout-form" id="logoutForm">
                            @csrf
                            <button type="submit" class="logout-btn" onclick="handleLogout(event)">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="profile-menu-item">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Đăng nhập</span>
                        </a>
                        <a href="{{ route('register') }}" class="profile-menu-item">
                            <i class="fas fa-user-plus"></i>
                            <span>Đăng ký</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Load cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking auth status...');
    console.log('Auth check: {{ auth()->check() ? "true" : "false" }}');
    
    // Delay loadCartCount để tránh conflict với redirect
    setTimeout(() => {
        loadCartCount();
    }, 100);
    
    initSearch();
    initProfileDropdown(); // Initialize profile dropdown
});

function loadCartCount() {
    // Chỉ load cart count nếu user đã đăng nhập
    if ({{ auth()->check() ? 'true' : 'false' }}) {
        fetch('/cart/count')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const cartCountElement = document.getElementById('cartCount');
                if (cartCountElement) {
                    const count = Number(data.count || 0);
                    cartCountElement.textContent = count;
                    if (count === 0) {
                        cartCountElement.style.display = 'none';
                    } else {
                        cartCountElement.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading cart count:', error);
                // Ẩn cart count nếu có lỗi
                const cartCountElement = document.getElementById('cartCount');
                if (cartCountElement) {
                    cartCountElement.style.display = 'none';
                }
            });
    } else {
        // Nếu chưa đăng nhập thì ẩn cart count
        const cartCountElement = document.getElementById('cartCount');
        if (cartCountElement) {
            cartCountElement.style.display = 'none';
        }
    }
}

function initSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const searchDropdown = document.getElementById('searchDropdown');
    const headerSearch = document.getElementById('headerSearch');
    const searchBtn = document.getElementById('searchBtn');
    const searchResults = document.getElementById('searchResults');

    // Toggle search dropdown
    searchToggle.addEventListener('click', function(e) {
        e.preventDefault();
        searchDropdown.classList.toggle('active');
        if (searchDropdown.classList.contains('active')) {
            headerSearch.focus();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchToggle.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.classList.remove('active');
            searchResults.innerHTML = '';
        }
    });

    // Search on input
    let searchTimeout;
    headerSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Search on button click
    searchBtn.addEventListener('click', function() {
        const query = headerSearch.value.trim();
        if (query) {
            window.location.href = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;
        }
    });

    // Search on Enter key
    headerSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;
            }
        }
    });
}

function performSearch(query) {
    const searchResults = document.getElementById('searchResults');
    
    fetch(`{{ route('products.index') }}?search=${encodeURIComponent(query)}&limit=5`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.products && data.products.length > 0) {
            let resultsHtml = '<div class="search-results-list">';
            data.products.forEach(product => {
                resultsHtml += `
                    <div class="search-result-item" onclick="goToProduct(${product.id})">
                        <div class="result-image">
                            <img src="${product.image_url || '/storage/placeholder.jpg'}" alt="${product.name}">
                        </div>
                        <div class="result-info">
                            <div class="result-name">${product.name}</div>
                            <div class="result-price">${new Intl.NumberFormat('vi-VN').format(product.price)} VNĐ</div>
                        </div>
                    </div>
                `;
            });
            resultsHtml += '</div>';
            searchResults.innerHTML = resultsHtml;
        } else {
            searchResults.innerHTML = '<div class="no-results">Không tìm thấy sản phẩm</div>';
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="search-error">Có lỗi xảy ra</div>';
    });
}

function goToProduct(productId) {
    window.location.href = `/products/${productId}`;
}

function initProfileDropdown() {
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileAvatar = document.getElementById('profileAvatar');
    const profileName = document.getElementById('profileName');
    const profileEmail = document.getElementById('profileEmail');
    const avatarInput = document.getElementById('avatarInput');

    // Toggle profile dropdown
    profileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        profileDropdown.classList.toggle('active');
        if (profileDropdown.classList.contains('active')) {
            // Update profile info if user is logged in
            if ({{ auth()->check() && auth()->user() ? 'true' : 'false' }}) {
                profileName.textContent = '{{ auth()->user() ? (auth()->user()->name ?? "Khách") : "Khách" }}';
                profileEmail.textContent = '{{ auth()->user() ? (auth()->user()->email ?? "") : "" }}';
                profileAvatar.src = '{{ auth()->user() && auth()->user()->avatar ? asset("storage/" . auth()->user()->avatar) : asset("images/default-avatar.png") }}';
            } else {
                profileName.textContent = 'Khách';
                profileEmail.textContent = '';
                profileAvatar.src = '{{ asset("images/default-avatar.png") }}';
            }
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    // Handle avatar upload
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && {{ auth()->check() ? 'true' : 'false' }}) {
            const formData = new FormData();
            formData.append('avatar', file);

            fetch('{{ route("profile.update-avatar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    profileAvatar.src = data.avatar_url;
                    alert('Ảnh đại diện đã được cập nhật thành công!');
                } else {
                    alert('Có lỗi khi cập nhật ảnh đại diện: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating avatar:', error);
                alert('Có lỗi khi cập nhật ảnh đại diện.');
            });
        } else if (!{{ auth()->check() ? 'true' : 'false' }}) {
            alert('Vui lòng đăng nhập để cập nhật ảnh đại diện.');
        }
    });
}

// Handle logout with better error handling
function handleLogout(event) {
    event.preventDefault();
    
    const form = document.getElementById('logoutForm');
    const csrfToken = form.querySelector('input[name="_token"]').value;
    
    // Disable button to prevent double click
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng xuất...';
    
    fetch('{{ route("logout") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json, text/plain, */*'
        },
        body: '_token=' + encodeURIComponent(csrfToken)
    })
    .then(response => {
        if (response.ok) {
            // Redirect to home page
            window.location.href = '/';
        } else {
            throw new Error('Logout failed');
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sign-out-alt"></i><span>Đăng xuất</span>';
        
        // Try traditional form submit as fallback
        form.submit();
    });
}
</script>
 