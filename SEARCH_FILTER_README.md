# Chức Năng Tìm Kiếm Và Bộ Lọc Sản Phẩm

## Tổng Quan

Hệ thống tìm kiếm và bộ lọc sản phẩm được xây dựng cho shop mỹ phẩm với các tính năng:

-   🔍 **Tìm kiếm real-time** theo tên sản phẩm và mô tả
-   📂 **Lọc theo danh mục** sản phẩm
-   💰 **Lọc theo khoảng giá** với slider trực quan
-   🔄 **Sắp xếp** theo nhiều tiêu chí
-   ⚡ **AJAX search** không reload trang
-   📱 **Responsive design** cho mọi thiết bị

## Cấu Trúc Files

```
resources/
├── views/
│   ├── components/
│   │   ├── search-filter.blade.php      # Component tìm kiếm chính
│   │   └── product-card.blade.php       # Component hiển thị sản phẩm
│   └── user/products/
│       ├── index.blade.php              # Trang danh sách sản phẩm
│       └── partials/
│           └── product-grid.blade.php   # Grid sản phẩm cho AJAX
├── css/
│   └── search-filter.css               # Styles cho tìm kiếm
└── js/
    └── search-filter.js                # JavaScript xử lý tìm kiếm

app/
└── Http/Controllers/User/
    └── ProductController.php            # Controller xử lý tìm kiếm
```

## Cài Đặt

### 1. Cập Nhật ProductController

Controller đã được cập nhật với các phương thức:

-   `index()`: Hiển thị sản phẩm với bộ lọc
-   `search()`: API endpoint cho AJAX search

### 2. Thêm Routes

```php
Route::get('/products/search', [UserProductController::class, 'search'])->name('products.search');
```

### 3. Import CSS và JS

Thêm vào layout chính:

```html
<link rel="stylesheet" href="{{ asset('css/search-filter.css') }}" />
<script src="{{ asset('js/search-filter.js') }}"></script>
```

## Sử Dụng

### 1. Hiển Thị Component Tìm Kiếm

```blade
<x-search-filter :categories="$categories" :priceRange="$priceRange" />
```

### 2. Hiển Thị Sản Phẩm

```blade
<x-product-card :product="$product" />
```

### 3. Sử Dụng JavaScript

```javascript
// Khởi tạo search filter
const searchFilter = new SearchFilter();

// Tìm kiếm theo query
searchFilter.search("kem dưỡng ẩm");

// Lấy bộ lọc hiện tại
const currentFilters = searchFilter.getCurrentFilters();

// Đặt bộ lọc programmatically
searchFilter.setFilters({
    category: "1",
    min_price: "100000",
    max_price: "500000",
});
```

## Tính Năng

### Tìm Kiếm

-   **Real-time search**: Tìm kiếm tự động sau 500ms khi nhập
-   **Full-text search**: Tìm theo tên và mô tả sản phẩm
-   **Debouncing**: Tránh gọi API quá nhiều

### Bộ Lọc

-   **Danh mục**: Lọc theo loại sản phẩm
-   **Giá**: Khoảng giá từ tối thiểu đến tối đa
-   **Slider giá**: Giao diện trực quan với 2 slider
-   **Sắp xếp**: Theo thời gian, tên, giá

### Giao Diện

-   **Responsive**: Hoạt động tốt trên mọi thiết bị
-   **Loading states**: Hiển thị trạng thái đang tìm kiếm
-   **Error handling**: Xử lý lỗi một cách thân thiện
-   **URL sync**: Cập nhật URL theo bộ lọc

## Tùy Chỉnh

### 1. Thay Đổi Thời Gian Debounce

```javascript
// Trong search-filter.js
this.searchTimeout = setTimeout(() => {
    this.performSearch();
}, 1000); // Thay đổi từ 500ms thành 1000ms
```

### 2. Thêm Bộ Lọc Mới

```php
// Trong ProductController
if ($request->filled('brand')) {
    $query->where('brand_id', $request->brand);
}
```

### 3. Tùy Chỉnh Style

```css
/* Trong search-filter.css */
.search-filter-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

## API Endpoints

### GET /products/search

**Parameters:**

-   `search`: Từ khóa tìm kiếm
-   `category`: ID danh mục
-   `min_price`: Giá tối thiểu
-   `max_price`: Giá tối đa
-   `sort`: Tiêu chí sắp xếp (created_at, name, price)
-   `order`: Thứ tự (asc, desc)

**Response:**

```json
{
    "html": "<div>...</div>",
    "pagination": "<div>...</div>"
}
```

## Xử Lý Lỗi

Hệ thống tự động xử lý các trường hợp:

-   **Network errors**: Hiển thị thông báo lỗi và nút retry
-   **Empty results**: Hiển thị trạng thái không có kết quả
-   **Invalid filters**: Validate và hiển thị lỗi

## Performance

-   **Lazy loading**: Chỉ tải sản phẩm khi cần
-   **Caching**: Sử dụng Laravel query caching
-   **Optimized queries**: Eager loading relationships
-   **Debounced search**: Giảm số lượng API calls

## Browser Support

-   Chrome 60+
-   Firefox 55+
-   Safari 12+
-   Edge 79+

## Troubleshooting

### 1. Tìm Kiếm Không Hoạt Động

Kiểm tra:

-   Console errors
-   Network tab trong DevTools
-   Route đã được định nghĩa chưa

### 2. Bộ Lọc Không Cập Nhật

Kiểm tra:

-   JavaScript events đã được bind chưa
-   Form elements có đúng ID không
-   AJAX response format

### 3. Style Không Hiển Thị

Kiểm tra:

-   CSS file đã được import chưa
-   Tailwind CSS classes có đúng không
-   Browser cache

## Đóng Góp

Để cải thiện hệ thống:

1. Fork repository
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## License

MIT License - Xem file LICENSE để biết thêm chi tiết.
