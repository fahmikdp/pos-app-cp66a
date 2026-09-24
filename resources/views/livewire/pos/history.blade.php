<div class="space-y-5">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Riwayat Transaksi Saya</h1>
        <p class="text-sm text-gray-500">Daftar transaksi yang Anda proses</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col sm:flex-row gap-3">
        <x-wireui-input wire:model.live.debounce.300ms="search" placeholder="Cari no. invoice..." icon="magnifying-glass" class="flex-1 max-w-xs" />
        <x-wireui-datetime-picker wire:model.live="filterDate" placeholder="DD/MM/YYYY" parse-format="YYYY-MM-DD" display-format="DD/MM/YYYY" without-time class="w-52" />
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">No</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ $loop->iteration + $transactions->firstItem() - 1 }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $trx->invoice_number }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $trx->items->count() }} item</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium {{ $trx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} rounded-full">
                                {{ $trx->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <x-heroicons::outline.clock class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">Belum ada transaksi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $transactions->links('livewire.custom-pagination') }}
    </div>
</div>
