<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderReviewSeeder extends Seeder
{
    /**
     * Tạo 20-30 đơn hàng giả lập với nhiều sản phẩm khác nhau
     * Mục đích: Tạo dữ liệu cho hệ thống gợi ý View History và Popularity-Based
     */
    public function run()
    {
        // Lấy hoặc tạo users
        $users = User::where('role', 'user')->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(10)->create(['role' => 'user']);
        }

        // Lấy tất cả sản phẩm có sẵn với variants
        $products = Product::with('variants')->where('is_active', true)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('Không có sản phẩm nào trong database. Vui lòng chạy ProductSeeder trước.');
            return;
        }

        // Các trạng thái đơn hàng (ưu tiên delivered cho view history)
        $statuses = ['delivered', 'delivered', 'delivered', 'delivered', 'shipped', 'processing', 'pending'];
        
        // Phương thức thanh toán
        $paymentMethods = ['cash_on_delivery', 'online_payment', 'bank_transfer'];
        
        // Địa chỉ mẫu
        $addresses = [
            '123 Đường Nguyễn Huệ, Quận 1, TP.HCM',
            '456 Đường Lê Lợi, Quận 3, TP.HCM',
            '789 Đường Trần Hưng Đạo, Quận 5, TP.HCM',
            '321 Đường Võ Văn Tần, Quận 3, TP.HCM',
            '654 Đường Điện Biên Phủ, Quận Bình Thạnh, TP.HCM',
            '987 Đường Cách Mạng Tháng 8, Quận 10, TP.HCM',
            '147 Đường Hoàng Văn Thụ, Quận Phú Nhuận, TP.HCM',
            '258 Đường Nguyễn Văn Cừ, Quận 5, TP.HCM',
        ];

        // Tạo 20-30 đơn hàng
        $orderCount = rand(20, 30);
        $this->command->info("Đang tạo {$orderCount} đơn hàng...");

        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users->random();
            
            // Tạo order_code unique
            $orderCode = 'ORD-' . strtoupper(Str::random(8));
            while (Order::where('order_code', $orderCode)->exists()) {
                $orderCode = 'ORD-' . strtoupper(Str::random(8));
            }

            // Chọn trạng thái ngẫu nhiên (ưu tiên delivered)
            $status = $statuses[array_rand($statuses)];
            
            // Tạo ngày đặt hàng trong vòng 30-90 ngày gần đây
            $createdAt = Carbon::now()->subDays(rand(30, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone ?? '0' . rand(100000000, 999999999),
                'customer_address' => $addresses[array_rand($addresses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'note' => rand(0, 1) ? 'Đơn hàng demo cho hệ thống gợi ý' : null,
                'voucher_code' => null,
                'discount_amount' => 0,
                'total_price' => 0,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $status === 'delivered' 
                    ? $createdAt->copy()->addDays(rand(2, 7)) 
                    : $createdAt->copy()->addHours(rand(1, 24)),
            ]);

            $totalPrice = 0;
            
            // Mỗi đơn hàng có 2-5 sản phẩm khác nhau
            $itemCount = rand(2, 5);
            $usedProductIds = []; // Đảm bảo không trùng sản phẩm trong cùng đơn hàng

            for ($j = 0; $j < $itemCount; $j++) {
                // Lấy sản phẩm chưa được sử dụng trong đơn hàng này
                $availableProducts = $products->filter(function($product) use ($usedProductIds) {
                    return !in_array($product->id, $usedProductIds);
                });

                if ($availableProducts->isEmpty()) {
                    break; // Nếu hết sản phẩm thì dừng
                }

                $product = $availableProducts->random();
                $usedProductIds[] = $product->id;

                // Chọn variant ngẫu nhiên nếu có, hoặc null
                $variant = null;
                if ($product->variants->isNotEmpty() && rand(0, 1)) {
                    $variant = $product->variants->random();
                }

                // Giá và số lượng
                $unitPrice = $variant ? $variant->price : $product->price;
                $quantity = rand(1, 3); // Số lượng từ 1-3
                $itemTotal = $unitPrice * $quantity;

                // Tạo order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'variant_name' => $variant ? $variant->name : null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $totalPrice += $itemTotal;
            }

            // Thêm phí vận chuyển (30,000 VNĐ)
            $shippingFee = 30000;
            $totalPrice += $shippingFee;

            // Cập nhật tổng tiền đơn hàng
            $order->update(['total_price' => $totalPrice]);

            $this->command->info("Đã tạo đơn hàng #{$order->id}: {$order->order_code} với {$itemCount} sản phẩm - Tổng: " . number_format($totalPrice, 0, ',', '.') . '₫');
        }

        $this->command->info("Hoàn thành! Đã tạo {$orderCount} đơn hàng với nhiều sản phẩm khác nhau.");
    }
}

