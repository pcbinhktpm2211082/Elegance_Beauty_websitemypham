document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const searchBtn = document.getElementById('search-btn');
    const searchResults = document.getElementById('search-results');

    // Danh sách sản phẩm mẫu (trong thực tế sẽ lấy từ backend)
    const products = [
        {
            id: 1,
            name: 'Kem Dưỡng Da Cao Cấp',
            price: 1200000,
            category: 'skincare',
            image: 'product1.jpg'
        },
        {
            id: 2,
            name: 'Serum Trẻ Hóa Chuyên Sâu',
            price: 1500000,
            category: 'skincare',
            image: 'product2.jpg'
        },
        {
            id: 3,
            name: 'Mặt Nạ Dưỡng Ẩm Cao Cấp',
            price: 800000,
            category: 'skincare',
            image: 'product3.jpg'
        },
        {
            id: 4,
            name: 'Son Môi Lì Cao Cấp',
            price: 450000,
            category: 'makeup',
            image: 'product5.jpg'
        },
        {
            id: 5,
            name: 'Phấn Nền Mịn Màng',
            price: 750000,
            category: 'makeup',
            image: 'product6.jpg'
        }
    ];

    function renderProducts(filteredProducts) {
        searchResults.innerHTML = `
            <div class="product-grid">
                ${filteredProducts.map(product => `
                    <div class="product-card">
                        <img src="${product.image}" alt="${product.name}">
                        <h4>${product.name}</h4>
                        <p>${product.price.toLocaleString()} VNĐ</p>
                        <div class="product-actions">
                            <button class="add-to-cart" data-id="${product.id}">Thêm Vào Giỏ</button>
                            <button class="view-details" data-id="${product.id}">Chi Tiết</button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function searchProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        const filteredProducts = products.filter(product => {
            const matchesSearch = product.name.toLowerCase().includes(searchTerm);
            const matchesCategory = selectedCategory === '' || product.category === selectedCategory;
            
            return matchesSearch && matchesCategory;
        });

        renderProducts(filteredProducts);
    }

    // Sự kiện tìm kiếm
    searchBtn.addEventListener('click', searchProducts);

    // Tìm kiếm khi nhấn Enter
    searchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            searchProducts();
        }
    });

    // Thêm sản phẩm vào giỏ hàng
    searchResults.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-to-cart')) {
            const productId = e.target.getAttribute('data-id');
            const product = products.find(p => p.id === parseInt(productId));
            
            alert(`Đã thêm ${product.name} vào giỏ hàng`);
            // Trong thực tế, bạn sẽ thêm logic quản lý giỏ hàng
        }
    });

    // Hiển thị tất cả sản phẩm ban đầu
    renderProducts(products);
}); 