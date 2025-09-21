<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Order;

class DashboardController extends Controller
{
public function index()
{
    $cacheKey = 'admin_dashboard_stats_v1';

    $data = Cache::remember($cacheKey, 60, function () {
        $allStatuses = [
            'pending'    => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped'    => 'Đang giao hàng',
            'delivered'  => 'Đã hoàn thành',
            'cancelled'  => 'Đã hủy',
        ];

        $orderStatsRaw = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $orderStats = collect($allStatuses)->map(function ($label, $key) use ($orderStatsRaw) {
            return $orderStatsRaw[$key] ?? 0;
        });

        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $year = Carbon::now()->year;

        $monthlyRevenueRaw = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereYear('orders.created_at', $year)
            ->where('orders.status', 'delivered')
            ->selectRaw(
                'MONTH(orders.created_at) as month, SUM(order_items.quantity * order_items.unit_price) as revenue'
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = collect(range(1, 12))->mapWithKeys(function ($m) use ($monthlyRevenueRaw) {
            $found = $monthlyRevenueRaw->firstWhere('month', $m);
            return [$m => $found ? (float)$found->revenue : 0];
        });

        return [
            'orderStats' => $orderStats,
            'topProducts' => $topProducts,
            'monthlyRevenue' => $monthlyRevenue,
            'year' => $year,
            'allStatuses' => $allStatuses,
        ];
    });

    // Ép chắc chắn thành mảng để Blade nhận biến
    return view('admin.dashboard', (array) $data);
}


}
