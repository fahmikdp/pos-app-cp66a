<div class="flex flex-col lg:flex-row gap-5 lg:h-[calc(100vh-8rem)] h-auto">
    {{-- Left: Menu Grid --}}
    <div class="flex-1 flex flex-col gap-4 min-h-0 lg:h-full lg:overflow-hidden">
        {{-- Search & Filter Kategori --}}
        <div class="bg-white rounded-xl shadow-sm p-4 space-y-3 flex-shrink-0">
            <x-wireui-input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari menu..."
                icon="magnifying-glass"
            />
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="$set('filterCategoryId', null)"
                    class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors {{ $filterCategoryId === null ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="$set('filterCategoryId', {{ $cat->id }})"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors {{ $filterCategoryId === $cat->id ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Menu Items Grid --}}
        <div class="flex-1 overflow-y-auto pr-1 min-h-0 max-h-[45vh] lg:max-h-none">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($menuItems as $item)
                    <button
                        wire:click="addToCart({{ $item->id }})"
                        class="bg-white rounded-xl shadow-sm p-4 text-left hover:shadow-md hover:ring-2 hover:ring-primary-200 transition-all group"
                    >
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-primary-200 transition-colors">
                            <x-heroicons::solid.shopping-bag class="w-5 h-5 text-primary-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-800 leading-tight mb-1">{{ $item->name }}</p>
                        <p class="text-xs text-gray-400 mb-2">Stok: {{ $item->stock_qty }}</p>
                        <p class="text-sm font-bold text-primary-600">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </button>
                @empty
                    <div class="col-span-4 py-16 text-center text-gray-400">
                        <x-heroicons::outline.clipboard-document-list class="w-10 h-10 mx-auto mb-2" />
                        <p class="text-sm">Tidak ada menu tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Cart & Payment --}}
    <div class="w-full lg:w-80 flex flex-col bg-white rounded-xl shadow-sm overflow-hidden min-h-0 flex-shrink-0 lg:flex-shrink mb-6 lg:mb-0 lg:sticky lg:top-4 lg:h-[calc(100vh-8rem)]">
        {{-- Cart Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 flex-shrink-0">
            <h2 class="text-sm font-semibold text-gray-800">Keranjang</h2>
            @if(count($cart) > 0)
                <button wire:click="clearCart" class="text-xs text-red-500 hover:text-red-700">Kosongkan</button>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="overflow-y-auto px-4 py-2 space-y-1 flex-1 min-h-0 max-h-48 lg:max-h-none">
            @forelse($cart as $itemId => $cartItem)
                <div class="flex items-center gap-2 py-1.5 border-b border-gray-50">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-800 truncate">{{ $cartItem['name'] }}</p>
                        <p class="text-xs text-gray-500">Rp {{ number_format($cartItem['price'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="decreaseQty({{ $itemId }})" class="w-5 h-5 rounded-full bg-gray-100 hover:bg-primary-100 text-gray-600 hover:text-primary-700 flex items-center justify-center text-xs font-bold transition-colors">−</button>
                        <span class="w-5 text-center text-xs font-semibold">{{ $cartItem['qty'] }}</span>
                        <button wire:click="increaseQty({{ $itemId }})" class="w-5 h-5 rounded-full bg-gray-100 hover:bg-primary-100 text-gray-600 hover:text-primary-700 flex items-center justify-center text-xs font-bold transition-colors">+</button>
                    </div>
                    <p class="text-xs font-semibold text-gray-800 w-16 text-right">Rp {{ number_format($cartItem['subtotal'], 0, ',', '.') }}</p>
                    <button wire:click="removeFromCart({{ $itemId }})" class="text-red-400 hover:text-red-600 flex-shrink-0">
                        <x-heroicons::outline.x-mark class="w-3.5 h-3.5" />
                    </button>
                </div>
            @empty
                <div class="py-4 text-center text-gray-400">
                    <x-heroicons::outline.shopping-cart class="w-6 h-6 mx-auto mb-1" />
                    <p class="text-xs">Pilih menu dari atas</p>
                </div>
            @endforelse
        </div>

        {{-- Total & Payment --}}
        <div class="border-t border-gray-100 px-4 py-3 space-y-2 flex-shrink-0 bg-white lg:sticky lg:bottom-0 lg:z-10">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">Total</span>
                <span class="text-sm font-bold text-gray-800">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
            </div>

            <x-wireui-input
                wire:model.live="paymentAmount"
                label="Nominal Bayar (Rp)"
                type="number"
                placeholder="0"
                min="0"
            />

            @if((float)$paymentAmount >= $this->total && $this->total > 0)
                <div class="flex items-center justify-between bg-emerald-50 rounded-lg px-3 py-1.5">
                    <span class="text-xs text-emerald-700">Kembalian</span>
                    <span class="text-sm font-bold text-emerald-700">Rp {{ number_format($this->change, 0, ',', '.') }}</span>
                </div>
            @endif

            <x-wireui-button
                primary
                class="w-full"
                label="Proses Pembayaran"
                wire:click="processPayment"
                wire:loading.attr="disabled"
                :disabled="count($cart) === 0"
            />
        </div>
    </div>

    {{-- Receipt Modal --}}
    <x-wireui-modal wire:model="showReceiptModal" align="center" max-width="sm" spacing="px-1 py-6 sm:p-6">
        <x-wireui-card title="Transaksi Berhasil!" padding="p-4 sm:p-6">
            @if($lastTransaction)
                <x-wireui-alert title="Pembayaran Berhasil" positive class="mb-4">
                    Transaksi {{ $lastTransaction->invoice_number }} telah berhasil diproses.
                </x-wireui-alert>
                <div class="text-center mb-4">
                    <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <x-heroicons::solid.check-circle class="w-8 h-8 text-emerald-600" />
                    </div>
                    <p class="text-sm text-gray-500">{{ $lastTransaction->invoice_number }}</p>
                </div>

                <div id="receipt-area" class="border border-dashed border-gray-300 rounded-lg p-4 font-mono text-xs">
                    <div class="text-center mb-3">
                        <p class="font-bold text-base">POS APP</p>
                        <p class="text-gray-500">Sistem Kasir Digital</p>
                        <p class="text-gray-400">{{ $lastTransaction->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-400">Kasir: {{ auth()->user()->name }}</p>
                        <p class="text-gray-400">{{ $lastTransaction->invoice_number }}</p>
                    </div>
                    <div class="border-t border-dashed border-gray-300 pt-2 mb-2">
                        @foreach($lastTransaction->items as $item)
                            <div class="flex justify-between gap-2 mb-1">
                                <div class="flex-1">
                                    <p>{{ $item->item_name }}</p>
                                    <p class="text-gray-400">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <p class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-dashed border-gray-300 pt-2 space-y-1">
                        <div class="flex justify-between font-bold">
                            <span>TOTAL</span>
                            <span>Rp {{ number_format($lastTransaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Bayar</span>
                            <span>Rp {{ number_format($lastTransaction->payment_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembalian</span>
                            <span>Rp {{ number_format($lastTransaction->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="border-t border-dashed border-gray-300 pt-2 mt-2 text-center text-gray-400">
                        <p>Terima kasih atas kunjungan Anda!</p>
                    </div>
                </div>
            @endif

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3">
                    <x-wireui-button flat label="Tutup" wire:click="$set('showReceiptModal', false)" />
                    @if($lastTransaction)
                        <x-wireui-button
                            primary
                            icon="printer"
                            label="Cetak Struk"
                            onclick="window.print()"
                        />
                    @endif
                </div>
            </x-slot>
        </x-wireui-card>
    </x-wireui-modal>
</div>
