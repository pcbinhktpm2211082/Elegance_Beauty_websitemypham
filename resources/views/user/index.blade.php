@extends('layouts.user')

@section('title', 'Trang chủ')

@section('content')
    <main>
        <!-- Banners: Left slider + Right two static -->
        <section id="banners-grid" class="relative">
            <style>
                #banners-grid{max-width:1400px;margin:0 auto;padding:50px 5%;box-sizing:border-box;background-color:#ffffff}
                #banners-grid .banners-grid{display:flex;gap:16px;align-items:stretch;width:100%}
                #banners-grid .left-banner{flex:2;position:relative;min-width:0}
                #banners-grid .right-banners{flex:1;display:flex;flex-direction:column;gap:16px;min-width:0}
                #banners-grid .banner-card{position:relative;border-radius:8px;overflow:hidden;height:100%;width:100%}
                #banners-grid .banner-card img{width:100%;height:100%;object-fit:cover;display:block}
                #banners-grid .banner-link{display:block;height:100%}
                #banners-grid .banner-overlay{position:absolute;inset:auto 0 0 0;padding:12px 16px;background:linear-gradient(180deg,rgba(0,0,0,0) 0%,rgba(0,0,0,.55) 100%);color:#fff}
                #banners-grid .banner-title{margin:0;font-size:18px;font-weight:700}
                #banners-grid .banner-description{margin:4px 0 0;font-size:14px;opacity:.9}
                /* Left slider */
                #banners-grid .left-slider{position:relative;height:100%}
                #banners-grid .left-track{display:flex;transition:transform .5s ease;height:100%}
                #banners-grid .left-slide{min-width:100%;height:100%;flex-shrink:0}
                #banners-grid .nav-btn{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.45);color:#fff;border:none;width:36px;height:36px;border-radius:999px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10}
                #banners-grid .nav-prev{left:8px}
                #banners-grid .nav-next{right:8px}
                /* Aspect ratios */
                #banners-grid .left-banner .banner-card{aspect-ratio:16/9;min-height:280px}
                #banners-grid .right-banners .banner-card{aspect-ratio:16/9;min-height:130px}
                @media (max-width: 768px){
                    #banners-grid{padding:30px 16px}
                    #banners-grid .banners-grid{flex-direction:column}
                    #banners-grid .left-banner,#banners-grid .right-banners{flex:none}
                    #banners-grid .left-banner .banner-card{min-height:220px}
                    #banners-grid .right-banners .banner-card{min-height:140px}
                }
            </style>
            @if(($leftBanners && $leftBanners->count() > 0) || $rightTop || $rightBottom)
                <div class="banners-grid">
                    <div class="left-banner">
                        <div class="banner-card">
                            <div class="left-slider">
                                <div class="left-track" id="leftBannerTrack">
                                    @foreach(($leftBanners ?? collect()) as $lb)
                                        <div class="left-slide">
                                            @if($lb->link)
                                                <a class="banner-link" href="{{ $lb->link }}">
                                                    <img src="{{ asset('storage/' . $lb->image) }}" alt="{{ $lb->title }}">
                                                    @if($lb->title || $lb->description)
                                                        <div class="banner-overlay">
                                                            @if($lb->title)
                                                                <h2 class="banner-title">{{ $lb->title }}</h2>
                                                            @endif
                                                            @if($lb->description)
                                                                <p class="banner-description">{{ $lb->description }}</p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </a>
                                            @else
                                                <img src="{{ asset('storage/' . $lb->image) }}" alt="{{ $lb->title }}">
                                                @if($lb->title || $lb->description)
                                                    <div class="banner-overlay">
                                                        @if($lb->title)
                                                            <h2 class="banner-title">{{ $lb->title }}</h2>
                                                        @endif
                                                        @if($lb->description)
                                                            <p class="banner-description">{{ $lb->description }}</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if(($leftBanners->count() ?? 0) > 1)
                                    <button class="nav-btn nav-prev" type="button" onclick="changeLeftBanner(-1)">‹</button>
                                    <button class="nav-btn nav-next" type="button" onclick="changeLeftBanner(1)">›</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="right-banners">
                        @if($rightTop)
                            <div class="banner-card">
                                @if($rightTop->link)
                                    <a class="banner-link" href="{{ $rightTop->link }}">
                                        <img src="{{ asset('storage/' . $rightTop->image) }}" alt="{{ $rightTop->title }}">
                                        @if($rightTop->title || $rightTop->description)
                                            <div class="banner-overlay">
                                                @if($rightTop->title)
                                                    <h2 class="banner-title">{{ $rightTop->title }}</h2>
                                                @endif
                                                @if($rightTop->description)
                                                    <p class="banner-description">{{ $rightTop->description }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                @else
                                    <img src="{{ asset('storage/' . $rightTop->image) }}" alt="{{ $rightTop->title }}">
                                    @if($rightTop->title || $rightTop->description)
                                        <div class="banner-overlay">
                                            @if($rightTop->title)
                                                <h2 class="banner-title">{{ $rightTop->title }}</h2>
                                            @endif
                                            @if($rightTop->description)
                                                <p class="banner-description">{{ $rightTop->description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                        @if($rightBottom)
                            <div class="banner-card">
                                @if($rightBottom->link)
                                    <a class="banner-link" href="{{ $rightBottom->link }}">
                                        <img src="{{ asset('storage/' . $rightBottom->image) }}" alt="{{ $rightBottom->title }}">
                                        @if($rightBottom->title || $rightBottom->description)
                                            <div class="banner-overlay">
                                                @if($rightBottom->title)
                                                    <h2 class="banner-title">{{ $rightBottom->title }}</h2>
                                                @endif
                                                @if($rightBottom->description)
                                                    <p class="banner-description">{{ $rightBottom->description }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                @else
                                    <img src="{{ asset('storage/' . $rightBottom->image) }}" alt="{{ $rightBottom->title }}">
                                    @if($rightBottom->title || $rightBottom->description)
                                        <div class="banner-overlay">
                                            @if($rightBottom->title)
                                                <h2 class="banner-title">{{ $rightBottom->title }}</h2>
                                            @endif
                                            @if($rightBottom->description)
                                                <p class="banner-description">{{ $rightBottom->description }}</p>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="no-banner">
                    <p>Chưa có banner nào được thêm</p>
                </div>
            @endif
        </section>

        <section id="featured-products">
            <h3>Sản Phẩm Nổi Bật</h3>
            <div class="featured-slider-container">
                <button class="slider-nav prev-btn" onclick="changeSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="featured-slider">
                    <div class="slider-track">
                        @forelse($featuredProducts as $product)
                            @if($product)
                                <div class="product-card slider-item">
                                    {{-- Hiển thị ảnh bìa sản phẩm --}}
                                    @php
                                        $cover = $product->coverOrFirstImage ?? null;
                                    @endphp

                                    @if($cover)
                                        <img src="{{ asset('storage/' . $cover) }}" alt="{{ $product->name ?? 'Sản phẩm' }}">
                                    @else
                                        <img src="{{ asset('storage/placeholder.jpg') }}" alt="Không có ảnh">
                                    @endif

                                    <h4>{{ $product->name ?? 'Tên sản phẩm' }}</h4>
                                    
                                    <div class="product-price-action-wrapper">
                                        <p class="product-price">{{ number_format($product->price ?? 0) }} VNĐ</p>
                                        
                                        <div class="product-actions">
                                            <a href="{{ route('user.products.show', $product->id) }}" class="view-details">
                                                Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="no-products">
                                <p>Không có sản phẩm nổi bật</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <button class="slider-nav next-btn" onclick="changeSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="slider-dots">
                @php
                    $totalSlides = ceil(count($featuredProducts ?? collect()) / 5);
                @endphp
                @for($i = 0; $i < $totalSlides; $i++)
                    <span class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></span>
                @endfor
            </div>
        </section>

        <!-- Gợi ý dành riêng cho bạn -->
        <section id="personalized-recommendations" style="margin-top: 60px; padding: 40px 5%; background: #f9fafb;">
            <div style="max-width: 1400px; margin: 0 auto;">
                <h3 style="font-size: 28px; font-weight: 700; margin-bottom: 30px; text-align: center; color: #1f2937;">
                    💡 Gợi ý dành riêng cho bạn
                </h3>
                <p style="text-align: center; color: #6b7280; margin-bottom: 30px; font-size: 16px;">
                    Dựa trên hồ sơ da của bạn
                </p>
                <div id="personalized-products-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                    <div style="text-align: center; padding: 40px; color: #6b7280;">
                        Đang tải gợi ý...
                    </div>
                </div>
            </div>
        </section>

        <section id="about-brand">
            <div class="brand-content">
                <h3>Câu Chuyện Thương Hiệu</h3>
                <p>Elegance Beauty ra đời với sứ mệnh mang đến những sản phẩm mỹ phẩm cao cấp, an toàn và hiệu quả. Chúng tôi tin rằng mỗi người đều xứng đáng được tỏa sáng.</p>
                <a href="{{ url('/about') }}" class="learn-more">Tìm Hiểu Thêm</a>
            </div>
        </section>
    </main>

    <script>
        // Left banner slider
        let currentLeftBanner = 0;
        const leftTrack = document.getElementById('leftBannerTrack');
        const totalLeftBanners = {{ ($leftBanners && $leftBanners->count()) ? $leftBanners->count() : 0 }};

        function changeLeftBanner(direction){
            if(!leftTrack || totalLeftBanners <= 1) return;
            currentLeftBanner += direction;
            if(currentLeftBanner < 0){ currentLeftBanner = totalLeftBanners - 1; }
            if(currentLeftBanner >= totalLeftBanners){ currentLeftBanner = 0; }
            leftTrack.style.transform = `translateX(${-currentLeftBanner * 100}%)`;
        }

        if(totalLeftBanners > 1){
            setInterval(() => changeLeftBanner(1), 5000);
        }

        // Load personalized recommendations
        async function loadPersonalizedRecommendations() {
            const container = document.getElementById('personalized-products-container');
            try {
                const response = await fetch('/recommendations/content-based?limit=8');
                const data = await response.json();
                
                if (data.success && data.products && data.products.length > 0) {
                    container.innerHTML = data.products.map(product => {
                        const image = product.images && product.images.length > 0 
                            ? `/storage/${product.images[0].image_path}` 
                            : '/storage/placeholder.jpg';
                        const price = new Intl.NumberFormat('vi-VN').format(product.price);
                        
                        return `
                            <div class="product-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer;" 
                                 onclick="window.location.href='/products/${product.id}'"
                                 onmouseover="this.style.transform='translateY(-5px)'"
                                 onmouseout="this.style.transform='translateY(0)'">
                                <img src="${image}" alt="${product.name}" style="width: 100%; height: 200px; object-fit: cover;">
                                <div style="padding: 15px;">
                                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #1f2937; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${product.name}</h4>
                                    <p style="font-size: 16px; font-weight: 700; color: #3b82f6; margin: 0;">${price} VNĐ</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Chưa có thông tin da để gợi ý. <a href="/profile/edit" style="color: #3b82f6;">Cập nhật hồ sơ da của bạn</a></div>';
                }
            } catch (error) {
                console.error('Error loading personalized recommendations:', error);
                container.innerHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Không thể tải gợi ý</div>';
            }
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPersonalizedRecommendations();
        });

        // Product slider logic
        let currentSlide = 0;
        const totalSlides = {{ ceil(count($featuredProducts ?? collect()) / 4) }};
        const sliderTrack = document.querySelector('.slider-track');
        const dots = document.querySelectorAll('.dot');

        function changeSlide(direction) {
            currentSlide += direction;
            
            if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            } else if (currentSlide >= totalSlides) {
                currentSlide = 0;
            }
            
            updateSlider();
        }

        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateSlider();
        }

        function updateSlider() {
            if (sliderTrack) {
                const translateX = -currentSlide * 100;
                sliderTrack.style.transform = `translateX(${translateX}%)`;
            }
            
            // Cập nhật dots
            if (dots && dots.length > 0) {
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }
        }

        // Touch/swipe support cho mobile
        let startX = 0;
        let endX = 0;

        if (sliderTrack) {
            sliderTrack.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });

            sliderTrack.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                handleSwipe();
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = startX - endX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    changeSlide(1); // Swipe left - next
                } else {
                    changeSlide(-1); // Swipe right - prev
                }
            }
        }

        function addToCart(productId) {
            if (!productId) {
                console.error('Product ID is required');
                return;
            }
            
            // Kiểm tra xem sản phẩm có biến thể không
            fetch(`/api/products/${productId}/check-variants`)
                .then(response => response.json())
                .then(data => {
                    if (data.has_variants) {
                        // Nếu có biến thể thì redirect đến trang chi tiết để chọn biến thể
                        if (productId) {
                            window.location.href = `/products/${productId}`;
                        }
                    } else {
                        // Nếu không có biến thể thì thêm trực tiếp vào giỏ hàng
                        addToCartDirect(productId);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback: redirect đến trang chi tiết
                    if (productId) {
                        window.location.href = `/products/${productId}`;
                    }
                });
        }

        function addToCartDirect(productId) {
            if (!productId) {
                console.error('Product ID is required');
                return;
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }
            
            // Prepare data using JSON
            const data = {
                product_id: productId,
                quantity: 1,
                _token: csrfToken.getAttribute('content')
            };
            
            console.log('Adding to cart directly:', data);
            
            // Send request
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Kiểm tra content-type
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Nếu không phải JSON, đọc text để debug
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(data => {
                console.log('Response data:', data);
                if (data && data.success) {
                    showNotification(data.message || 'Thêm vào giỏ hàng thành công!', 'success');
                    
                    // Update cart count in header
                    const cartCountElement = document.getElementById('cartCount');
                    if (cartCountElement) {
                        const count = Number(data.cart_count || 0);
                        cartCountElement.textContent = count;
                        cartCountElement.style.display = count === 0 ? 'none' : 'block';
                    }
                } else {
                    showNotification(data?.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Có lỗi xảy ra: ' + (error.message || 'Lỗi không xác định'), 'error');
            });
        }

        function showNotification(message, type) {
            if (!message) {
                console.error('Notification message is required');
                return;
            }
            
            const notification = document.createElement('div');
            notification.className = `notification ${type || 'info'}`;
            notification.textContent = message;
            
            // Style the notification
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.padding = '15px 20px';
            notification.style.borderRadius = '5px';
            notification.style.color = 'white';
            notification.style.fontWeight = 'bold';
            notification.style.zIndex = '9999';
            notification.style.animation = 'slideIn 0.3s ease';
            
            if (type === 'success') {
                notification.style.backgroundColor = '#4CAF50';
            } else if (type === 'error') {
                notification.style.backgroundColor = '#f44336';
            } else {
                notification.style.backgroundColor = '#2196F3';
            }
            
            if (document.body) {
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    if (notification && notification.parentNode) {
                        notification.remove();
                    }
                }, 3000);
            }
        }
    </script>
@endsection
