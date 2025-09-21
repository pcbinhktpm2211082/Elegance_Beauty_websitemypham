@extends('layouts.user')

@section('title', 'Trang chủ')

@section('content')
    <main>
        <section id="hero">
            <div class="hero-content">
                <h2>Vẻ Đẹp Tinh Tế</h2>
                <p>Khám phá bộ sưu tập mỹ phẩm cao cấp, được chế tác để làm nổi bật vẻ đẹp tự nhiên của bạn</p>
                <div class="hero-actions">
                    <a href="{{ route('products.index') }}" class="cta-button">Khám Phá Ngay</a>
                    <a href="{{ route('brand.story') }}" class="cta-button secondary">Tìm hiểu thêm</a>
                </div>
            </div>
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
                                    <p>{{ number_format($product->price ?? 0) }} VNĐ</p>
                                    
                                    <div class="product-actions">
                                        <a href="{{ route('user.products.show', $product->id) }}" class="view-details">
                                            Xem chi tiết
                                        </a>
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


        <section id="about-brand">
            <div class="brand-content">
                <h3>Câu Chuyện Thương Hiệu</h3>
                <p>Elegance Beauty ra đời với sứ mệnh mang đến những sản phẩm mỹ phẩm cao cấp, an toàn và hiệu quả. Chúng tôi tin rằng mỗi người đều xứng đáng được tỏa sáng.</p>
                <a href="{{ url('/about') }}" class="learn-more">Tìm Hiểu Thêm</a>
            </div>
        </section>
    </main>

    <script>
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
