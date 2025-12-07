<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
    * Chuyển hướng người dùng sang Google để xác thực.
    */
    public function redirect(Request $request): RedirectResponse
    {
        $redirectUrl = $this->buildRedirectUrl($request);

        return Socialite::driver('google')
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
    * Xử lý callback từ Google sau khi người dùng xác thực.
    */
    public function callback(Request $request): RedirectResponse
    {
        $redirectUrl = $this->buildRedirectUrl($request);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUrl)
                ->user();
        } catch (\Throwable $e) {
            Log::error('Google login failed', ['error' => $e->getMessage()]);
            return redirect()->route('login')->withErrors([
                'oauth' => 'Không đăng nhập được bằng Google. Vui lòng thử lại.',
            ]);
        }

        if (!$googleUser->getEmail()) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Tài khoản Google không cung cấp email. Vui lòng dùng cách khác.',
            ]);
        }

        $user = User::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user && $user->role === 'admin') {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Tài khoản admin không hỗ trợ đăng nhập Google.',
            ]);
        }

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Người dùng Google',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'avatar' => $googleUser->getAvatar(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'role' => 'user',
                'status' => true,
            ]);
        } else {
            $user->update([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        Auth::login($user, true);

        // Kiểm tra nếu user chưa điền đầy đủ thông tin cá nhân thì redirect đến trang chỉnh sửa profile
        if (!$user->hasCompleteProfile()) {
            return redirect()->route('profile.edit')
                ->with('info', 'Vui lòng điền đầy đủ thông tin cá nhân để tiếp tục sử dụng dịch vụ.');
        }

        return redirect()->intended('/');
    }

    /**
     * Build redirect_uri khớp tuyệt đối với URL hiện tại, kể cả khi chạy subfolder/public.
     */
    private function buildRedirectUrl(Request $request): string
    {
        $relative = route('google.callback', [], false); // /auth/google/callback
        return $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $relative;
    }
}

