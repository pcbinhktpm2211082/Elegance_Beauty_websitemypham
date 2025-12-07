<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productTypes = [
            // Các loại cần lọc loại da (requires_skin_type_filter = true)
            ['name' => 'Skincare', 'requires_skin_type_filter' => true],
            ['name' => 'Serum', 'requires_skin_type_filter' => true],
            ['name' => 'Toner', 'requires_skin_type_filter' => true],
            ['name' => 'Moisturizer', 'requires_skin_type_filter' => true],
            ['name' => 'Cleanser', 'requires_skin_type_filter' => true],
            ['name' => 'Sunscreen', 'requires_skin_type_filter' => true],
            ['name' => 'Mask', 'requires_skin_type_filter' => true],
            ['name' => 'Eye Cream', 'requires_skin_type_filter' => true],
            ['name' => 'Essence', 'requires_skin_type_filter' => true],
            ['name' => 'Ampoule', 'requires_skin_type_filter' => true],
            ['name' => 'Exfoliator', 'requires_skin_type_filter' => true],
            ['name' => 'Tẩy tế bào chết', 'requires_skin_type_filter' => true],
            
            // Các loại không cần lọc loại da (requires_skin_type_filter = false)
            ['name' => 'Lip Balm', 'requires_skin_type_filter' => false],
            ['name' => 'Body Lotion', 'requires_skin_type_filter' => false],
            ['name' => 'Makeup', 'requires_skin_type_filter' => false],
        ];

        foreach ($productTypes as $type) {
            ProductType::firstOrCreate(
                ['name' => $type['name']],
                ['requires_skin_type_filter' => $type['requires_skin_type_filter']]
            );
        }
    }
}
