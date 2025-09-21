<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Variant;
use App\Models\User;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Lấy 5 user khách hàng bất kỳ (hoặc tạo mới nếu chưa có)
        $users = User::factory()->count(5)->create();

        // Lấy 5 sản phẩm có sẵn trong db, cùng lấy luôn variants của từng sản phẩm
        $products = Product::with('variants')->take(5)->get();

        foreach ($users as $user) {
            // Tạo 1-3 đơn hàng cho mỗi user
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_code' => 'DH' . Str::upper(Str::random(6)),
                    'status' => 'pending', // trạng thái đơn hàng
                    'total_price' => 0,    // tổng tiền ban đầu = 0
                    'customer_name' => $user->name,
                    'customer_phone' => '0123456789',
                    'customer_address' => '123 Đường ABC, Quận XYZ',
                    'payment_method' => 'Tiền mặt',
                    'note' => 'Đơn hàng test',
                ]);

                $totalPrice = 0;

                // Thêm 1-3 sản phẩm vào đơn hàng
                $itemCount = rand(1, 3);

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();

                    // Lấy 1 biến thể nếu có, hoặc null
                    $variant = $product->variants->isNotEmpty() ? $product->variants->random() : null;

                    $unitPrice = $variant ? $variant->price : $product->price;
                    $quantity = rand(1, 5);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $unitPrice * $quantity,
                        'product_name' => $product->name,
                        'variant_name' => $variant ? $variant->name : null,
                    ]);

                    $totalPrice += $unitPrice * $quantity;
                }

                // Cập nhật tổng tiền đơn hàng
                $order->update(['total_price' => $totalPrice]);
            }
        }
    }
}
