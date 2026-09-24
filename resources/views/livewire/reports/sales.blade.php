<div class="space-y-5">
    {{-- Header & Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Laporan Penjualan</h1>
            <p class="text-sm text-gray-500">Ringkasan pendapatan dan transaksi</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                <button wire:click="$set('period', 'daily')" class="px-4 py-2 text-sm font-medium transition-colors {{ $period === 'daily' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Harian</button>
                <button wire:click="$set('period', 'monthly')" class="px-4 py-2 text-sm font-medium transition-colors {{ $period === 'monthly' ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Bulanan</button>
            </div>
            @if($period === 'daily')
                <div class="w-36">
                    <x-wireui-select wire:model.live="filterMonth" :options="$monthOptions" option-label="label" option-value="value" :min-items-for-search="0" />
                </div>
            @endif
            <div class="w-28">
                <x-wireui-select wire:model.live="filterYear" :options="$yearOptions" option-label="label" option-value="value" :min-items-for-search="0" />
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.currency-dollar class="w-6 h-6 text-primary-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <x-heroicons::outline.receipt-percent class="w-6 h-6 text-blue-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalCount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Chart --}}
        <div class="bg-white rounded-xl shadow-sm p-5 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Grafik Pendapatan</h3>
            <div id="chart-sales" wire:ignore></div>
        </div>

        {{-- Top Items --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Produk Terlaris</h3>
            @forelse($topItems as $index => $item)
                <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700 flex-shrink-0">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item->item_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->total_qty }} porsi</p>
                    </div>
                    <p class="text-xs font-semibold text-primary-600 shrink-0">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <p class="text-sm">Belum ada data</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
var salesChart = null;

function renderSalesChart(labels, data) {
    if (salesChart) { salesChart.destroy(); }
    salesChart = new ApexCharts(document.querySelector('#chart-sales'), {
        series: [{ name: 'Pendapatan', data: data }],
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        colors: ['#dc2626'],
        xaxis: { categories: labels },
        yaxis: { labels: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
        dataLabels: { enabled: false },
        grid: { strokeDashArray: 4 },
        tooltip: { y: { formatter: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
        plotOptions: { bar: { borderRadius: 4 } },
    });
    salesChart.render();
}

document.addEventListener('livewire:initialized', () => {
    renderSalesChart(@json($chartLabels), @json($chartData));
});

document.addEventListener('livewire:updated', () => {
    renderSalesChart(@json($chartLabels), @json($chartData));
});
</script>
