<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.currency-dollar class="w-6 h-6 text-primary-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($todayTotal, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.receipt-percent class="w-6 h-6 text-blue-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.calendar-days class="w-6 h-6 text-emerald-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthTotal, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.exclamation-triangle class="w-6 h-6 text-amber-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Stok Hampir Habis</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lowStockItems }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Chart 7 hari --}}
        <div class="bg-white rounded-xl shadow-sm p-5 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                Pendapatan 7 Hari Terakhir
                @if(!$isAdmin)
                    <span class="text-xs text-gray-400 font-normal">(transaksi Anda)</span>
                @endif
            </h3>
            <div id="chart-revenue"></div>
        </div>

        {{-- Top Items --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Produk Terlaris Hari Ini</h3>
            @forelse($topItemsQuery as $index => $item)
                <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item->item_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->total_qty }} porsi</p>
                    </div>
                    <p class="text-xs font-semibold text-primary-600">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <x-heroicons::outline.shopping-cart class="w-8 h-8 mx-auto mb-2" />
                    <p class="text-sm">Belum ada transaksi hari ini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    var revenueChart = new ApexCharts(document.querySelector('#chart-revenue'), {
        series: [{ name: 'Pendapatan', data: @json($chartData) }],
        chart: { type: 'area', height: 250, toolbar: { show: false } },
        colors: ['#dc2626'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: @json($chartLabels) },
        yaxis: { labels: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
        dataLabels: { enabled: false },
        grid: { strokeDashArray: 4 },
        tooltip: { y: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
    });
    revenueChart.render();
});
</script>
