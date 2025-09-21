<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        return view('user.contact.index');
    }

    public function store(Request $request)
    {
        // Require authenticated user as a safety net (route also enforces auth)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi liên hệ.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'title.max' => 'Tiêu đề không được quá 255 ký tự',
            'content.required' => 'Vui lòng nhập nội dung',
            'content.min' => 'Nội dung phải có ít nhất 10 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'name.required' => 'Vui lòng nhập họ tên',
            'name.max' => 'Họ tên không được quá 255 ký tự',
        ]);

        try {
            $support = Support::create([
                'name' => $request->name,
                'email' => $request->email,
                'title' => $request->title,
                'message' => $request->content, // Sử dụng 'message' thay vì 'content'
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Tin nhắn của bạn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.');
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại.');
        }
    }
}
