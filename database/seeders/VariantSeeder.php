<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Variant;

class VariantSeeder extends Seeder
{
    public function run()
    {
        // Lấy 5 sản phẩm đầu tiên (hoặc tạo mới nếu chưa có)
        $products = Product::take(5)->get();

        if ($products->isEmpty()) {
            // Tạo mẫu 5 sản phẩm nếu chưa có (có thể tùy chỉnh)
            $products = Product::factory()->count(5)->create();
        }

        foreach ($products as $product) {
            // Tạo 1-3 biến thể cho mỗi sản phẩm
            $variantCount = rand(1, 3);
            for ($i = 0; $i < $variantCount; $i++) {
                Variant::create([
                    'product_id' => $product->id,
                    'name' => 'Variant ' . ($i + 1),
                    'price' => $product->price + rand(10000, 50000), // Giá variant cao hơn giá gốc một chút
                ]);
            }
        }
    }
}
