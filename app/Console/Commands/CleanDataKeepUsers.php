<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class CleanDataKeepUsers extends Command
{
    protected $signature = 'db:clean-keep-users {--confirm : Skip confirmation}';
    protected $description = 'Xóa tất cả dữ liệu trừ users và admins';

    public function handle()
    {
        $this->info('🗑️  Bắt đầu xóa dữ liệu (giữ lại users và admins)...');
        $this->newLine();

        // Xác nhận trước khi xóa
        if (!$this->option('confirm')) {
            if (!$this->confirm('Bạn có chắc chắn muốn xóa TẤT CẢ dữ liệu trừ users và admins?')) {
                $this->info('Đã hủy thao tác xóa dữ liệu.');
                return 0;
            }
        }

        try {
            // Tắt foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Xóa ảnh sản phẩm từ storage
            $this->info('📁 Đang xóa ảnh sản phẩm từ storage...');
            if (Storage::disk('public')->exists('products')) {
                Storage::disk('public')->deleteDirectory('products');
                $this->line('   ✅ Đã xóa thư mục products');
            }
            
            if (Storage::disk('public')->exists('variants')) {
                Storage::disk('public')->deleteDirectory('variants');
                $this->line('   ✅ Đã xóa thư mục variants');
            }

            // Danh sách các bảng cần xóa (không bao gồm users và admins)
            $tablesToClear = [
                'product_images',
                'product_variants', 
                'supports',
                'orders',
                'order_items',
                'products',
                'categories',
                'attributes',
                'attribute_values',
                'password_resets',
                'failed_jobs',
                'personal_access_tokens',
                'sessions'
            ];

            $bar = $this->output->createProgressBar(count($tablesToClear));
            $bar->start();

            foreach ($tablesToClear as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    if ($count > 0) {
                        DB::table($table)->truncate();
                        $this->line("🗑️  Đã xóa {$count} records từ bảng '{$table}'");
                    } else {
                        $this->line("⚪ Bảng '{$table}' đã trống");
                    }
                } else {
                    $this->line("⚠️  Bảng '{$table}' không tồn tại");
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            $this->info('✅ Hoàn thành! Đã xóa tất cả dữ liệu trừ users và admins.');
            $this->newLine();

            // Hiển thị thống kê còn lại
            $this->info('📊 Dữ liệu còn lại:');
            $usersCount = Schema::hasTable('users') ? DB::table('users')->count() : 0;
            $adminsCount = Schema::hasTable('admins') ? DB::table('admins')->count() : 0;
            
            $this->line("   👥 Users: {$usersCount} tài khoản");
            $this->line("   🔐 Admins: {$adminsCount} tài khoản");

        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return 1;
        } finally {
            // Bật lại foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }

        $this->newLine();
        $this->info('🎉 Xong!');
        return 0;
    }
}




