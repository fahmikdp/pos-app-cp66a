<?php

namespace App\Livewire;

use App\Models\MenuItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $todayQuery = Transaction::whereDate('created_at', today());
        $monthQuery = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);

        if (! $isAdmin) {
            $todayQuery->where('user_id', $user->id);
            $monthQuery->where('user_id', $user->id);
        }

        $todayTotal = (clone $todayQuery)->sum('total_amount');
        $todayCount = (clone $todayQuery)->count();
        $monthTotal = (clone $monthQuery)->sum('total_amount');
        $monthCount = (clone $monthQuery)->count();

        $lowStockItems = MenuItem::where('stock_qty', '<=', 5)->where('is_available', true)->count();

        $topItemsQuery = TransactionItem::selectRaw('menu_item_id, item_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('transaction', function ($q) use ($user, $isAdmin) {
                $q->whereDate('created_at', today());
                if (! $isAdmin) {
                    $q->where('user_id', $user->id);
                }
            })
            ->groupBy('menu_item_id', 'item_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $q = Transaction::whereDate('created_at', $date);
            if (! $isAdmin) {
                $q->where('user_id', $user->id);
            }
            $chartData[] = (float) $q->sum('total_amount');
        }

        return view('livewire.dashboard.index', compact(
            'todayTotal', 'todayCount', 'monthTotal', 'monthCount',
            'lowStockItems', 'topItemsQuery', 'chartLabels', 'chartData', 'isAdmin'
        ))->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
