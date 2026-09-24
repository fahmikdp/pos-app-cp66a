<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Cashier extends Component
{
    use WireUiActions;

    public ?int $filterCategoryId = null;

    public string $search = '';

    /** @var array<int, array{id: int, name: string, price: float, qty: int, subtotal: float}> */
    public array $cart = [];

    public string $paymentAmount = '';

    public bool $showReceiptModal = false;

    public ?int $lastTransactionId = null;

    public function updatingSearch(): void
    {
        $this->filterCategoryId = null;
    }

    public function addToCart(int $itemId): void
    {
        $item = MenuItem::find($itemId);
        if (! $item || ! $item->isInStock()) {
            $this->notification()->error('Gagal', 'Item tidak tersedia atau stok habis.');

            return;
        }

        $existingQty = $this->cart[$itemId]['qty'] ?? 0;
        if ($existingQty >= $item->stock_qty) {
            $this->notification()->warning('Stok Terbatas', 'Jumlah melebihi stok yang tersedia.');

            return;
        }

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['qty']++;
            $this->cart[$itemId]['subtotal'] = $this->cart[$itemId]['qty'] * $this->cart[$itemId]['price'];
        } else {
            $this->cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => (float) $item->price,
                'qty' => 1,
                'subtotal' => (float) $item->price,
            ];
        }
    }

    public function removeFromCart(int $itemId): void
    {
        unset($this->cart[$itemId]);
    }

    public function increaseQty(int $itemId): void
    {
        if (! isset($this->cart[$itemId])) {
            return;
        }
        $item = MenuItem::find($itemId);
        if ($item && $this->cart[$itemId]['qty'] >= $item->stock_qty) {
            $this->notification()->warning('Stok Terbatas', 'Jumlah melebihi stok yang tersedia.');

            return;
        }
        $this->cart[$itemId]['qty']++;
        $this->cart[$itemId]['subtotal'] = $this->cart[$itemId]['qty'] * $this->cart[$itemId]['price'];
    }

    public function decreaseQty(int $itemId): void
    {
        if (! isset($this->cart[$itemId])) {
            return;
        }
        if ($this->cart[$itemId]['qty'] <= 1) {
            $this->removeFromCart($itemId);

            return;
        }
        $this->cart[$itemId]['qty']--;
        $this->cart[$itemId]['subtotal'] = $this->cart[$itemId]['qty'] * $this->cart[$itemId]['price'];
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->paymentAmount = '';
    }

    public function getTotalProperty(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getChangeProperty(): float
    {
        $payment = (float) str_replace(['.', ','], ['', '.'], $this->paymentAmount);

        return max(0, $payment - $this->total);
    }

    public function processPayment(): void
    {
        if (empty($this->cart)) {
            $this->notification()->error('Keranjang Kosong', 'Tambahkan item terlebih dahulu.');

            return;
        }

        $payment = (float) str_replace(['.', ','], ['', '.'], $this->paymentAmount);

        if ($payment < $this->total) {
            $this->notification()->error('Pembayaran Kurang', 'Nominal pembayaran tidak mencukupi.');

            return;
        }

        try {
            DB::transaction(function () use ($payment) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'invoice_number' => Transaction::generateInvoiceNumber(),
                    'total_amount' => $this->total,
                    'payment_amount' => $payment,
                    'change_amount' => $payment - $this->total,
                    'status' => 'completed',
                ]);

                foreach ($this->cart as $itemId => $cartItem) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'menu_item_id' => $cartItem['id'],
                        'item_name' => $cartItem['name'],
                        'quantity' => $cartItem['qty'],
                        'unit_price' => $cartItem['price'],
                        'subtotal' => $cartItem['subtotal'],
                    ]);

                    MenuItem::where('id', $cartItem['id'])->decrement('stock_qty', $cartItem['qty']);
                }

                $this->lastTransactionId = $transaction->id;
            });

            $this->showReceiptModal = true;
            $this->cart = [];
            $this->paymentAmount = '';
            $this->notification()->success('Pembayaran Berhasil', 'Transaksi telah berhasil diproses.');
        } catch (\Throwable $e) {
            $this->notification()->error('Gagal', 'Transaksi gagal diproses. Silakan coba lagi.');
        }
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->where('stock_qty', '>', 0)
            ->when($this->filterCategoryId, fn ($q) => $q->where('category_id', $this->filterCategoryId))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->get();

        $lastTransaction = $this->lastTransactionId
            ? Transaction::with('items')->find($this->lastTransactionId)
            : null;

        return view('livewire.pos.cashier', compact('categories', 'menuItems', 'lastTransaction'))
            ->layout('layouts.app', ['title' => 'Kasir — POS']);
    }
}
