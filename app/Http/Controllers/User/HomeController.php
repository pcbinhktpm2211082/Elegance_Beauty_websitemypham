<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Lấy 6 sản phẩm nổi bật
            $featuredProducts = Product::where('is_featured', true)->take(6)->get();

            return view('user.index', compact('featuredProducts'));
        } catch (\Exception $e) {
            // Log lỗi và trả về view với dữ liệu rỗng
            Log::error('Error in HomeController@index: ' . $e->getMessage());
            $featuredProducts = collect();
            return view('user.index', compact('featuredProducts'));
        }
    }
}

