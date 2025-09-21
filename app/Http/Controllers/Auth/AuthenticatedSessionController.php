<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect admin to admin dashboard, others to home
        $user = Auth::user();
        if ($user && $user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        // Redirect to home page
        return redirect('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            Log::info('Logout attempt', [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
                'csrf_token' => $request->input('_token')
            ]);
            
            // Logout user
            Auth::guard('web')->logout();

            // Invalidate session
            $request->session()->invalidate();

            // Regenerate CSRF token
            $request->session()->regenerateToken();

            // Clear any cart data
            $request->session()->forget('cart');
            $request->session()->forget('cart_count');
            $request->session()->forget('cart_total');

            Log::info('Logout successful');
            return redirect('/')->with('success', 'Đăng xuất thành công!');
        } catch (\Exception $e) {
            // Log error but still redirect
            Log::error('Logout error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId()
            ]);
            return redirect('/')->with('error', 'Có lỗi xảy ra khi đăng xuất.');
        }
    }
}
