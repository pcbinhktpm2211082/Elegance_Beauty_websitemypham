<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Lấy banners theo vị trí
            $leftBanners = Banner::where('is_active', true)
                ->where('position', 'left')
                ->orderBy('order')
                ->get();

            $rightTop = Banner::where('is_active', true)
                ->where('position', 'right_top')
                ->orderBy('order')
                ->first();

            $rightBottom = Banner::where('is_active', true)
                ->where('position', 'right_bottom')
                ->orderBy('order')
                ->first();

            // Lấy 6 sản phẩm nổi bật
            $featuredProducts = Product::where('is_featured', true)->take(6)->get();

            return view('user.index', [
                'featuredProducts' => $featuredProducts,
                'leftBanners' => $leftBanners,
                'rightTop' => $rightTop,
                'rightBottom' => $rightBottom,
            ]);
        } catch (\Exception $e) {
            // Log lỗi và trả về view với dữ liệu rỗng
            Log::error('Error in HomeController@index: ' . $e->getMessage());
            $featuredProducts = collect();
            return view('user.index', [
                'featuredProducts' => $featuredProducts,
                'leftBanners' => collect(),
                'rightTop' => null,
                'rightBottom' => null,
            ]);
        }
    }
}

