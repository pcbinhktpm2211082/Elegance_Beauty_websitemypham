<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $supports = Support::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());
        
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
