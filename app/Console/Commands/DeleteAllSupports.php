<?php

namespace App\Console\Commands;

use App\Models\Support;
use App\Models\SupportMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteAllSupports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supports:delete-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa tất cả cuộc trò chuyện hỗ trợ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Đang xóa tất cả cuộc trò chuyện hỗ trợ...');

        // Đếm số lượng cuộc trò chuyện
        $supportCount = Support::count();
        $messageCount = SupportMessage::count();

        if ($supportCount === 0) {
            $this->info('Không có cuộc trò chuyện nào để xóa.');
            return Command::SUCCESS;
        }

        // Xóa tất cả file đính kèm trước
        $this->info('Đang xóa file đính kèm...');
        $messages = SupportMessage::whereNotNull('attachment_path')->get();
        $deletedFiles = 0;
        
        foreach ($messages as $message) {
            if ($message->attachment_path && Storage::disk('public')->exists($message->attachment_path)) {
                Storage::disk('public')->delete($message->attachment_path);
                $deletedFiles++;
            }
        }

        // Xóa tất cả cuộc trò chuyện (messages sẽ tự động bị xóa do cascade)
        Support::query()->delete();

        $this->info("Đã xóa thành công:");
        $this->info("- {$supportCount} cuộc trò chuyện");
        $this->info("- {$messageCount} tin nhắn");
        $this->info("- {$deletedFiles} file đính kèm");

        return Command::SUCCESS;
    }
}
