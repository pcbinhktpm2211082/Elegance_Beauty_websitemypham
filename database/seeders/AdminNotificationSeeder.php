<?php

namespace Database\Seeders;

use App\Models\AdminNotification;
use Illuminate\Database\Seeder;

class AdminNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminNotification::query()->delete();

        AdminNotification::create([
            'title' => 'Chào mừng bạn đến với trang quản trị My Cosmetic Shop.',
            'message' => 'Bây giờ bạn có thể theo dõi đơn hàng, sản phẩm và người dùng tại đây.',
            'type' => 'info',
        ]);

        AdminNotification::create([
            'title' => 'Hệ thống hoạt động ổn định.',
            'message' => 'Mọi dịch vụ đều sẵn sàng, không có cảnh báo nào cần xử lý.',
            'type' => 'success',
        ]);
    }
}


