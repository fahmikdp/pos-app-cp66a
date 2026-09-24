<?php

namespace App\Livewire\Pos;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterDate = '';

    public bool $showDetailModal = false;

    public ?int $selectedTransactionId = null;

    public function openDetail(int $id): void
    {
        $this->selectedTransactionId = $id;
        $this->showDetailModal = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDate(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::with(['user', 'items'])
            ->where('user_id', auth()->id())
            ->when($this->search, fn ($q) => $q->where('invoice_number', 'like', '%'.$this->search.'%'))
            ->when($this->filterDate, fn ($q) => $q->whereDate('created_at', $this->filterDate))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.pos.history', compact('transactions'))
            ->layout('layouts.app', ['title' => 'Riwayat Transaksi Saya']);
    }
}
