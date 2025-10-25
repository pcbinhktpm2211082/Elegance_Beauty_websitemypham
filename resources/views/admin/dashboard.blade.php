@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-2xl font-bold text-center mb-8 text-gray-800">📊 Admin Dashboard</h1>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Tổng doanh thu -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg shadow-lg border border-blue-200">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-blue-600 text-sm font-medium">Tổng doanh thu</p>
                    <p class="text-2xl font-bold text-blue-800">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</p>
                </div>
                <div class="text-3xl text-blue-500">💰</div>
            </div>
        </div>

        <!-- Tổng đơn hàng -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg shadow-lg border border-green-200">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-green-600 text-sm font-medium">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-green-800">{{ $totalOrders }}</p>
                </div>
                <div class="text-3xl text-green-500">📦</div>
            </div>
        </div>

        <!-- Đơn hàng thành công -->
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-6 rounded-lg shadow-lg border border-purple-200">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-purple-600 text-sm font-medium">Đơn thành công</p>
                    <p class="text-2xl font-bold text-purple-800">{{ $completedOrders }}</p>
                </div>
                <div class="text-3xl text-purple-500">✅</div>
            </div>
        </div>

        <!-- Tổng sản phẩm -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-6 rounded-lg shadow-lg border border-orange-200">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-orange-600 text-sm font-medium">Tổng sản phẩm</p>
                    <p class="text-2xl font-bold text-orange-800">{{ $totalProducts }}</p>
                </div>
                <div class="text-3xl text-orange-500">🛍️</div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng -->
    <div class="mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📈 Doanh thu theo tháng ({{ $year }})</h2>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">🔥 Top sản phẩm bán chạy</h2>
            <div class="text-sm text-gray-500">Cập nhật lần cuối: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Tên sản phẩm</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Số lượng đã bán</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Doanh thu</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Tỷ lệ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $i => $product)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-black font-bold text-sm">
                                    {{ $i + 1 }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">ID: {{ $product->id }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ number_format($product->total_sold) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-lg font-bold text-blue-600">
                                {{ number_format($product->total_revenue ?? 0, 0, ',', '.') }} ₫
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $topProducts->isNotEmpty() ? min(100, ($product->total_sold / $topProducts->first()->total_sold) * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 font-medium">
                                    {{ $topProducts->isNotEmpty() ? number_format(($product->total_sold / $topProducts->first()->total_sold) * 100, 1) : 0 }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($topProducts->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-4">📦</div>
                                <h3 class="text-lg font-medium text-gray-500 mb-2">Chưa có dữ liệu</h3>
                                <p class="text-sm text-gray-400">Sản phẩm sẽ xuất hiện ở đây khi có đơn hàng</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Doanh thu theo tháng
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = {!! json_encode($monthlyRevenue->toArray()) !!};

const months = [
    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
];

const revenueValues = Object.values(revenueData).map(v => Math.round(v));

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: revenueValues,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3B82F6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        }
    }
});

</script>

@endsection
