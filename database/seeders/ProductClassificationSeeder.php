<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductClassification;

class ProductClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Loại da
        $skinTypes = [
            'Da Thường',
            'Da Khô',
            'Da Dầu/Nhờn',
            'Da Hỗn Hợp',
            'Da Nhạy Cảm',
            'Phù hợp với mọi tình trạng da',
        ];

        foreach ($skinTypes as $name) {
            ProductClassification::firstOrCreate(
                ['name' => $name, 'type' => 'skin_type'],
                ['name' => $name, 'type' => 'skin_type']
            );
        }

        // Các vấn đề da
        $skinConcerns = [
            'Mụn',
            'Lão hóa',
            'Tăng sắc tố',
            'Mất nước/Thiếu ẩm',
            'Da xỉn màu',
            'Lỗ chân lông to',
            'Đỏ da/Kích ứng',
        ];

        foreach ($skinConcerns as $name) {
            ProductClassification::firstOrCreate(
                ['name' => $name, 'type' => 'skin_concern'],
                ['name' => $name, 'type' => 'skin_concern']
            );
        }
    }
}
