<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->ajax() || $request->wantsJson()) {
                $request->headers->set('Accept', 'application/json');
            }
            
            // Xóa session cart cũ nếu có vấn đề
            if (session()->has('cart') && count(session('cart', [])) > 0) {
                $cart = session('cart', []);
                $hasDuplicateImages = false;
                $images = [];
                
                foreach ($cart as $item) {
                    if (isset($item['image'])) {
                        if (in_array($item['image'], $images)) {
                            $hasDuplicateImages = true;
                            break;
                        }
                        $images[] = $item['image'];
                    }
                }
                
                if ($hasDuplicateImages) {
                    Log::warning('Duplicate images detected in cart, clearing cart');
                    session()->forget('cart');
                    session()->forget('cart_count');
                    session()->forget('cart_total');
                }
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $cartItems = session('cart', []);
        $cartCount = $this->getCartCountValue();
        $cartTotal = $this->getCartTotal();
        
        return view('user.cart.index', compact('cartItems', 'cartCount', 'cartTotal'));
    }

    public function addToCart(Request $request)
    {
        try {
            Log::info('Cart addToCart called', $request->all());
            
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:99',
                'variant_id' => 'nullable|exists:product_variants,id'
            ]);

            $product = Product::findOrFail($request->product_id);
            $cart = session('cart', []);
            
            // Tạo key duy nhất cho sản phẩm (bao gồm cả variant_id nếu có)
            $cartKey = $request->product_id;
            if ($request->filled('variant_id')) {
                $cartKey = $request->product_id . '_' . $request->variant_id;
            }
            
            if (isset($cart[$cartKey])) {
                // Cập nhật số lượng nếu sản phẩm đã có
                $cart[$cartKey]['quantity'] += $request->quantity;
                $message = 'Cập nhật số lượng sản phẩm thành công!';
            } else {
                // Thêm mới sản phẩm với ảnh unique
                $variant = null;
                $productName = $product->name;
                $productPrice = $product->price;
                $productImage = $product->coverOrFirstImage;
                
                // Xử lý biến thể nếu có
                if ($request->filled('variant_id')) {
                    $variant = \App\Models\ProductVariant::find($request->variant_id);
                    if ($variant) {
                        $productName = $product->name . ' - ' . $variant->variant_name;
                        $productPrice = $variant->price ?? $product->price;
                        // Sử dụng ảnh của biến thể nếu có
                        if ($variant->image) {
                            $productImage = $variant->image;
                        }
                    }
                }
                
                // Xử lý ảnh sản phẩm
                if (empty($productImage) || $productImage === null) {
                    // Tạo tên file placeholder đơn giản hơn
                    $productImage = 'placeholder_' . $request->product_id . '.jpg';
                }
                // Ảnh từ product_images đã có đường dẫn đầy đủ (ví dụ: products/image1.jpg)
                
                $cart[$cartKey] = [
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'name' => $productName,
                    'price' => $productPrice,
                    'image' => $productImage,
                    'quantity' => $request->quantity
                ];
                
                // Log để debug
                Log::info('Product added to cart', [
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'product_name' => $productName,
                    'product_image_original' => $product->image,
                    'cart_image_final' => $cart[$cartKey]['image'],
                    'cart_key' => $cartKey,
                    'has_original_image' => !empty($product->image),
                    'has_variant' => $request->filled('variant_id')
                ]);
                $message = 'Thêm sản phẩm vào giỏ hàng thành công!';
            }
            
            // Lưu vào session
            session(['cart' => $cart]);
            
            $cartCount = $this->getCartCountValue();
            
            Log::info('Cart operation completed', ['message' => $message, 'cart_count' => $cartCount]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => $cartCount
                ])
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Cart addToCart error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json');
            }
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function updateQuantity(Request $request, $productId)
    {
        try {
            Log::info('Cart updateQuantity called', ['product_id' => $productId, 'request' => $request->all()]);
            
            $request->validate([
                'quantity' => 'required|integer|min:1|max:99',
                'variant_id' => 'nullable|integer'
            ]);

            $cart = session('cart', []);
            
            // Tạo key duy nhất cho sản phẩm (bao gồm cả variant_id nếu có)
            $cartKey = $productId;
            if ($request->filled('variant_id')) {
                $cartKey = $productId . '_' . $request->variant_id;
            }
            
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = $request->quantity;
                session(['cart' => $cart]);
                
                $cartCount = $this->getCartCountValue();
                $cartTotal = $this->getCartTotal();
                $itemSubtotal = $cart[$cartKey]['price'] * $request->quantity;

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Cập nhật số lượng thành công!',
                        'cart_count' => $cartCount,
                        'cart_total' => number_format($cartTotal, 0, ',', '.'),
                        'item_subtotal' => number_format($itemSubtotal, 0, ',', '.')
                    ])
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                }

                return redirect()->back()->with('success', 'Cập nhật số lượng thành công!');
            }
            
            throw new \Exception('Sản phẩm không tồn tại trong giỏ hàng');
            
        } catch (\Exception $e) {
            Log::error('Cart updateQuantity error: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json');
            }
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function removeFromCart(Request $request, $productId)
    {
        try {
            Log::info('Cart removeFromCart called', ['product_id' => $productId, 'request' => $request->all()]);
            
            $request->validate([
                'variant_id' => 'nullable|integer'
            ]);
            
            $cart = session('cart', []);
            
            // Tạo key duy nhất cho sản phẩm (bao gồm cả variant_id nếu có)
            $cartKey = $productId;
            if ($request->filled('variant_id')) {
                $cartKey = $productId . '_' . $request->variant_id;
            }
            
            Log::info('Cart removeFromCart processing', [
                'cartKey' => $cartKey,
                'available_keys' => array_keys($cart),
                'variant_id' => $request->variant_id
            ]);
            
            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
                session(['cart' => $cart]);
                
                $cartCount = $this->getCartCountValue();
                $cartTotal = $this->getCartTotal();

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công!',
                        'cart_count' => $cartCount,
                        'cart_total' => number_format($cartTotal, 0, ',', '.')
                    ])
                    ->header('Content-Type', 'application/json')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                }

                return redirect()->back()->with('success', 'Xóa sản phẩm khỏi giỏ hàng thành công!');
            }
            
            throw new \Exception('Sản phẩm không tồn tại trong giỏ hàng');
            
        } catch (\Exception $e) {
            Log::error('Cart removeFromCart error: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json');
            }
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function clearCart()
    {
        try {
            Log::info('Cart clearCart called');
            session()->forget('cart');
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng!'
                ])
                ->header('Content-Type', 'application/json')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            }
            
            return redirect()->back()->with('success', 'Đã xóa tất cả sản phẩm khỏi giỏ hàng!');
            
        } catch (\Exception $e) {
            Log::error('Cart clearCart error: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json');
            }
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Thêm method debug để xem session cart
    public function debugCart()
    {
        $cart = session('cart', []);
        Log::info('Debug cart session', ['cart' => $cart]);
        
        // Debug từng item
        foreach ($cart as $productId => $item) {
            Log::info("Cart item {$productId}", [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'name' => $item['name'],
                'image' => $item['image'],
                'price' => $item['price'],
                'image_path' => asset('storage/' . $item['image']),
                'image_exists' => file_exists(public_path('storage/' . $item['image']))
            ]);
        }
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart)
        ]);
    }

    // Thêm method để xóa hoàn toàn session cart
    public function forceClearCart()
    {
        try {
            Log::info('Force clear cart called');
            
            // Xóa tất cả session liên quan đến cart
            session()->forget('cart');
            session()->forget('cart_count');
            session()->forget('cart_total');
            
            // Xóa cache nếu có
            if (function_exists('cache')) {
                cache()->forget('cart_' . auth()->id());
            }
            
            // Xóa tất cả session bắt đầu bằng 'cart'
            $sessionKeys = array_keys(session()->all());
            foreach ($sessionKeys as $key) {
                if (strpos($key, 'cart') === 0) {
                    session()->forget($key);
                }
            }
            
            Log::info('Cart completely cleared');
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa hoàn toàn giỏ hàng!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Force clear cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm method để xóa session cart theo user ID
    public function clearUserCart()
    {
        try {
            $userId = auth()->id();
            Log::info('Clear user cart called', ['user_id' => $userId]);
            
            // Xóa session cart của user cụ thể
            session()->forget("cart_user_{$userId}");
            session()->forget("cart_count_user_{$userId}");
            session()->forget("cart_total_user_{$userId}");
            
            // Xóa session cart chung
            session()->forget('cart');
            session()->forget('cart_count');
            session()->forget('cart_total');
            
            // Xóa cache
            if (function_exists('cache')) {
                cache()->forget("cart_{$userId}");
                cache()->forget('cart_' . auth()->id());
            }
            
            Log::info('User cart cleared', ['user_id' => $userId]);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa giỏ hàng của user!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Clear user cart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCartCount()
    {
        try {
            $cart = session('cart', []);
            $count = 0;
            
            foreach ($cart as $item) {
                $count += $item['quantity'];
            }
            
            // Trả về JSON response cho API
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'count' => $count,
                    'success' => true
                ]);
            }
            // Trả về giá trị số cho nội bộ
            return $count;
            
        } catch (\Exception $e) {
            Log::error('Cart getCartCount error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'count' => 0,
                    'success' => false,
                    'message' => 'Có lỗi xảy ra'
                ]);
            }
            return 0;
        }
    }

    // Helper nội bộ để lấy số lượng dạng int, tránh JSONResponse
    private function getCartCountValue(): int
    {
        try {
            $cart = session('cart', []);
            $count = 0;
            foreach ($cart as $item) {
                $count += $item['quantity'];
            }
            return (int)$count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getCartTotal()
    {
        try {
            $cart = session('cart', []);
            $total = 0;
            
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            return $total;
            
        } catch (\Exception $e) {
            Log::error('Cart getCartTotal error: ' . $e->getMessage());
            return 0;
        }
    }

    // Thêm method test để kiểm tra database products
    public function testProducts()
    {
        try {
            $products = \App\Models\Product::select('id', 'name', 'image')->get();
            $productsData = [];
            
            foreach ($products as $product) {
                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image ?? 'NULL',
                    'has_image' => !empty($product->image)
                ];
            }
            
            Log::info('Test products data', ['products' => $productsData]);
            
            return response()->json([
                'success' => true,
                'products' => $productsData,
                'total_products' => count($productsData),
                'products_with_images' => count(array_filter($productsData, fn($p) => $p['has_image'])),
                'products_without_images' => count(array_filter($productsData, fn($p) => !$p['has_image']))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm method để cập nhật ảnh sản phẩm trong database
    public function updateProductImages()
    {
        try {
            $products = \App\Models\Product::with('images')->get();
            $updatedCount = 0;
            foreach ($products as $product) {
                // Kiểm tra xem sản phẩm có ảnh trong bảng product_images không
                if ($product->images->isEmpty()) {
                    // Tạo tên file placeholder đơn giản
                    $newImageName = 'products/placeholder_' . $product->id . '.jpg';
                    
                    // Tạo record trong bảng product_images
                    $product->images()->create([
                        'image_path' => $newImageName,
                        'is_cover' => true,
                        'sort_order' => 0
                    ]);
                    
                    $updatedCount++;
                    Log::info('Product image updated', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'new_image' => $newImageName
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật ảnh cho {$updatedCount} sản phẩm!",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Update product images error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    // Thêm method để test ảnh trực tiếp
    public function testImage($imagePath)
    {
        try {
            $fullPath = public_path('storage/' . $imagePath);
            $exists = file_exists($fullPath);
            $size = $exists ? filesize($fullPath) : 0;
            
            Log::info('Test image', [
                'image_path' => $imagePath,
                'full_path' => $fullPath,
                'exists' => $exists,
                'size' => $size,
                'url' => asset('storage/' . $imagePath)
            ]);
            
            return response()->json([
                'success' => true,
                'image_path' => $imagePath,
                'full_path' => $fullPath,
                'exists' => $exists,
                'size' => $size,
                'url' => asset('storage/' . $imagePath)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test image error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm method để test ảnh trực tiếp từ database
    public function testProductImage($productId)
    {
        try {
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found']);
            }
            
            $imagePath = $product->coverOrFirstImage;
            $fullPath = public_path('storage/' . $imagePath);
            $exists = file_exists($fullPath);
            $size = $exists ? filesize($fullPath) : 0;
            
            Log::info('Test product image', [
                'product_id' => $productId,
                'product_name' => $product->name,
                'image_path' => $imagePath,
                'full_path' => $fullPath,
                'exists' => $exists,
                'size' => $size,
                'url' => asset('storage/' . $imagePath),
                'storage_url' => url('storage/' . $imagePath)
            ]);
            
            return response()->json([
                'success' => true,
                'product_id' => $productId,
                'product_name' => $product->name,
                'image_path' => $imagePath,
                'full_path' => $fullPath,
                'exists' => $exists,
                'size' => $size,
                'url' => asset('storage/' . $imagePath),
                'storage_url' => url('storage/' . $imagePath)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Test product image error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    // Thêm method để kiểm tra database products
    public function checkDatabaseProducts()
    {
        try {
            $products = \App\Models\Product::with('images')->get();
            $productsData = [];
            foreach ($products as $product) {
                $coverImage = $product->coverOrFirstImage;
                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image ?? 'NULL',
                    'cover_image' => $coverImage ?? 'NULL',
                    'has_image' => !empty($product->image),
                    'has_cover_image' => !empty($coverImage),
                    'image_path' => $product->image ? 'products/' . $product->image : null,
                    'cover_image_path' => $coverImage,
                    'full_path' => $product->image ? public_path('storage/products/' . $product->image) : null,
                    'cover_full_path' => $coverImage ? public_path('storage/' . $coverImage) : null,
                    'file_exists' => $product->image ? file_exists(public_path('storage/products/' . $product->image)) : false,
                    'cover_file_exists' => $coverImage ? file_exists(public_path('storage/' . $coverImage)) : false
                ];
            }
            
            Log::info('Database products check', ['products' => $productsData]);
            
            return response()->json([
                'success' => true,
                'products' => $productsData,
                'total_products' => count($productsData),
                'products_with_images' => count(array_filter($productsData, fn($p) => $p['has_image'])),
                'products_with_cover_images' => count(array_filter($productsData, fn($p) => $p['has_cover_image'])),
                'products_without_images' => count(array_filter($productsData, fn($p) => !$p['has_image']))
            ]);
            
        } catch (\Exception $e) {
            Log::error('Check database products error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}
