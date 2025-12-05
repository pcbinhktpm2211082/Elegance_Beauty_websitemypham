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
        
        $supportsQuery = Support::query()
            ->with('messages')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('updated_at');

        $supports = $supportsQuery->get();

        $currentSupport = null;
        if ($supports->isNotEmpty()) {
            $selectedId = $request->input('support_id');
            $currentSupport = $supports->firstWhere('id', $selectedId) ?? $supports->first();
            $currentSupport->load(['messages' => function ($query) {
                $query->with('sender')->orderBy('created_at');
            }]);
        }
        
        return view('admin.supports.index', [
            'supports' => $supports,
            'currentSupport' => $currentSupport,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Support $support)
    {
        $support->load(['messages' => function ($query) {
            $query->with('sender')->orderBy('created_at');
        }]);

        return view('admin.supports.show', [
            'support' => $support,
            'messages' => $support->messages,
        ]);
    }

    public function messagesFragment(Support $support)
    {
        $support->load(['messages' => function ($query) {
            $query->with('sender')->orderBy('created_at');
        }]);

        return view('admin.supports._messages', [
            'support' => $support,
            'messages' => $support->messages,
        ]);
    }

    public function sendMessage(Request $request, Support $support)
    {
        $request->validate([
            'message' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'image|max:4096',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
        ]);

        if (!$request->filled('message') && !$request->hasFile('attachments')) {
            return redirect()->back()->with('error', 'Vui lòng nhập nội dung hoặc chọn hình ảnh.');
        }

        if ($request->filled('message')) {
            $support->messages()->create([
                'sender_id' => Auth::id(),
                'is_admin' => true,
                'message' => $request->input('message', ''),
                'attachment_path' => null,
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPath = $file->store('support_attachments', 'public');
                $support->messages()->create([
                    'sender_id' => Auth::id(),
                    'is_admin' => true,
                    'message' => null,
                    'attachment_path' => $attachmentPath,
                ]);
            }
        }

        $support->message = $request->message ?: 'Đã gửi hình ảnh';
        if ($request->filled('status')) {
            $support->status = $request->status;
        } elseif ($support->status === 'pending') {
            $support->status = 'processing';
        }
        $support->save();

        return redirect()->route('admin.supports.index', ['support_id' => $support->id])
            ->with('success', 'Đã gửi phản hồi cho khách hàng.');
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
