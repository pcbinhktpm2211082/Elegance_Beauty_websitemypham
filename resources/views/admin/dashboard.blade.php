@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <h1 class="text-xl font-bold text-center mb-4">📊 Dashboard - Thống kê đơn hàng</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Card: tổng đơn theo trạng thái -->
        <div class="col-span-1 md:col-span-2 bg-white p-6 rounded-lg shadow flex justify-center">
            <div class="flex flex-col items-center">
                <h2 class="text-lg font-semibold mb-4">Tỷ lệ trạng thái đơn hàng</h2>
                <canvas id="orderChart" style="width:400px; height:400px;"></canvas>
            </div>
        </div>

        <!-- Card: Doanh thu theo tháng -->
        <div class="bg-white p-6 rounded-lg shadow flex justify-center">
            <div class="flex flex-col items-center">
                <h2 class="text-lg font-semibold mb-4">Doanh thu theo tháng ({{ $year }})</h2>
                <canvas id="revenueChart" style="width:1200px; height:400px;"></canvas>
                <div class="mt-4 text-sm text-gray-600">Đơn vị: VNĐ</div>
            </div>
        </div>
    </div>

    <!-- Top 5 sản phẩm -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">🔥 Top 5 sản phẩm bán chạy</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-3 py-2 text-left">#</th>
                    <th class="px-3 py-2 text-left">Tên sản phẩm</th>
                    <th class="px-3 py-2 text-right">Số lượng đã bán</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topProducts as $i => $p)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $i + 1 }}</td>
                    <td class="px-3 py-2">{{ $p->name }}</td>
                    <td class="px-3 py-2 text-right">{{ (int)$p->total_sold }}</td>
                </tr>
                @endforeach
                @if($topProducts->isEmpty())
                <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Không có dữ liệu</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- Order status pie ---
    (function() {
        const ctx = document.getElementById('orderChart').getContext('2d');

        // Mapping trạng thái từ DB sang tiếng Việt
        const statusMap = {
            'pending': 'Chờ xử lý',
            'processing': 'Đang xử lý',
            'shipped': 'Đang giao hàng',
            'delivered': 'Đã hoàn thành',
            'cancelled': 'Đã hủy'
        };

        const rawLabels = {!! json_encode($orderStats->keys()->toArray()) !!};
        const rawData = {!! json_encode(array_values($orderStats->toArray())) !!};

        // Chuyển sang tiếng Việt
        const finalLabels = rawLabels.length ? rawLabels.map(s => statusMap[s] || s) : ['Không có dữ liệu'];
        const finalData = rawData.length ? rawData : [1];

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: finalLabels,
                datasets: [{
                    data: finalData,
                    backgroundColor: [
                        '#FBBF24', // vàng - chờ xử lý
                        '#60A5FA', // xanh dương - đang xử lý
                        '#8B5CF6', // tím - đang giao hàng
                        '#34D399', // xanh lá - đã hoàn thành
                        '#F87171'  // đỏ - đã hủy
                    ],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    })();

// --- Monthly revenue bar ---
(function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueObj = {!! json_encode($monthlyRevenue->toArray()) !!};

    const months = [
        'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
    ];

    const data = Object.values(revenueObj).map(v => Math.round(v));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data,
                backgroundColor: '#60A5FA',
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Tắt format mặc định
                        format: undefined,
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                        }
                    }
                }
            }
        },
        plugins: [{
            beforeInit: function(chart) {
                // Đảm bảo Chart.js không tự rút gọn
                chart.options.scales.y.ticks.format = undefined;
            }
        }]
    });
})();


</script>

@endsection
