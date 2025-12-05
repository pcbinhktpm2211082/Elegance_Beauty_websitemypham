<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Đánh dấu tất cả thông báo là đã đọc.
     */
    public function markAllRead(Request $request)
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back();
    }

    /**
     * Trả về số lượng thông báo chưa đọc (dùng cho icon chấm đỏ).
     */
    public function unreadCount()
    {
        $count = AdminNotification::where('is_read', false)->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }
}


