<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Livewire\Component;

class Sales extends Component
{
    public string $period = 'daily';

    public string $filterMonth = '';

    public string $filterYear = '';

    public function mount(): void
    {
        $this->filterMonth = now()->format('m');
        $this->filterYear = now()->format('Y');
    }

    public function render()
    {
        $year = (int) ($this->filterYear ?: now()->year);
        $month = (int) ($this->filterMonth ?: now()->month);

        if ($this->period === 'daily') {
            $transactions = Transaction::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('status', 'completed')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $chartLabels = $transactions->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('d/m'))->toArray();
            $chartData = $transactions->pluck('revenue')->map(fn ($v) => (float) $v)->toArray();
        } else {
            $transactions = Transaction::whereYear('created_at', $year)
                ->where('status', 'completed')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_amount) as revenue')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartLabels = $transactions->pluck('month')->map(fn ($m) => $monthNames[$m - 1])->toArray();
            $chartData = $transactions->pluck('revenue')->map(fn ($v) => (float) $v)->toArray();
        }

        $totalRevenue = $transactions->sum('revenue');
        $totalCount = $transactions->sum('count');

        $topItems = TransactionItem::selectRaw('item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('transaction', function ($q) use ($year, $month) {
                $q->where('status', 'completed')->whereYear('created_at', $year);
                if ($this->period === 'daily') {
                    $q->whereMonth('created_at', $month);
                }
            })
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $monthOptions = collect(range(1, 12))->map(fn ($m) => [
            'label' => Carbon::create()->month($m)->format('F'),
            'value' => str_pad($m, 2, '0', STR_PAD_LEFT),
        ])->toArray();

        $yearOptions = collect(range(now()->year - 2, now()->year))->map(fn ($y) => [
            'label' => (string) $y,
            'value' => (string) $y,
        ])->toArray();

        return view('livewire.reports.sales', compact(
            'chartLabels', 'chartData', 'totalRevenue', 'totalCount',
            'topItems', 'monthOptions', 'yearOptions'
        ))->layout('layouts.app', ['title' => 'Laporan Penjualan']);
    }
}
