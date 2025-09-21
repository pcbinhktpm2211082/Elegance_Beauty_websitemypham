<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        // Lấy tất cả supports từ contact form (không phân biệt created_by)
        $supports = Support::orderByDesc('created_at')->paginate(15);
        
        return view('admin.supports.index', compact('supports'));
    }

    public function show(Support $support)
    {
        return view('admin.supports.show', compact('support'));
    }

    public function markDone(Support $support)
    {
        $support->status = 'completed';
        $support->save();

        return redirect()->route('admin.supports.index')->with('success', 'Đã đánh dấu hoàn thành');
    }

    public function markProcessing(Support $support)
    {
        $support->status = 'processing';
        $support->save();

        return redirect()->route('admin.supports.index')->with('success', 'Đã đánh dấu đang xử lý');
    }

    public function markCancelled(Support $support)
    {
        $support->status = 'cancelled';
        $support->save();

        return redirect()->route('admin.supports.index')->with('success', 'Đã đánh dấu đã hủy');
    }
}
