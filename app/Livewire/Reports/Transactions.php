<?php

namespace App\Livewire\Reports;

use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterDate = '';

    public ?int $filterUserId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatingFilterUserId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::with(['user', 'items'])
            ->when($this->search, fn ($q) => $q->where('invoice_number', 'like', '%'.$this->search.'%'))
            ->when($this->filterDate, fn ($q) => $q->whereDate('created_at', $this->filterDate))
            ->when($this->filterUserId, fn ($q) => $q->where('user_id', $this->filterUserId))
            ->orderByDesc('created_at')
            ->paginate(15);

        $kasirOptions = User::where('role', 'kasir')->orWhere('role', 'superadmin')
            ->orderBy('name')->get()
            ->map(fn ($u) => ['label' => $u->name.' ('.ucfirst($u->role).')', 'value' => $u->id]);

        return view('livewire.reports.transactions', compact('transactions', 'kasirOptions'))
            ->layout('layouts.app', ['title' => 'Riwayat Semua Transaksi']);
    }
}
