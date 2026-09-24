<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Kelola Item Menu</h1>
            <p class="text-sm text-gray-500">Manajemen daftar menu, harga, dan stok</p>
        </div>
        <x-wireui-button primary label="Tambah Item Menu" icon="plus" wire:click="openCreate" />
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col sm:flex-row gap-3">
        <x-wireui-input wire:model.live.debounce.300ms="search" placeholder="Cari item menu..." icon="magnifying-glass" class="flex-1 max-w-xs" />
        <div class="w-52">
            <x-wireui-select
                wire:model.live="filterCategoryId"
                placeholder="Semua Kategori"
                :options="$categoryOptions"
                option-label="label"
                option-value="value"
                :min-items-for-search="0"
                clearable
            />
        </div>
        <div class="w-44">
            <x-wireui-select
                wire:model.live="filterAvailability"
                placeholder="Semua Status"
                :options="$availabilityOptions"
                option-label="label"
                option-value="value"
                :min-items-for-search="0"
                clearable
            />
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">No</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Item</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ $loop->iteration + $items->firstItem() - 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($item->stock_qty <= 5)
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">{{ $item->stock_qty }} (tipis)</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $item->stock_qty }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($item->is_available && $item->stock_qty > 0)
                                <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">Tersedia</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Habis</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openEdit({{ $item->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <x-heroicons::outline.pencil-square class="w-4 h-4" />
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus item ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <x-heroicons::outline.trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <x-heroicons::outline.clipboard-document-list class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">Tidak ada item menu ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $items->links('livewire.custom-pagination') }}
    </div>

    {{-- Modal --}}
    <x-wireui-modal wire:model="showModal" align="center" max-width="2xl" spacing="px-1 py-6 sm:p-6">
        <x-wireui-card :title="$menuItemId ? 'Edit Item Menu' : 'Tambah Item Menu'" padding="p-4 sm:p-6">
            <div class="space-y-5">
                <x-wireui-select
                    wire:model="categoryId"
                    label="Kategori"
                    placeholder="Pilih kategori..."
                    :options="$categoryOptions"
                    option-label="label"
                    option-value="value"
                    :min-items-for-search="0"
                />
                <x-wireui-input wire:model="name" label="Nama Item" placeholder="cth: Ayam Geprek Sambal Ijo" />
                <x-wireui-textarea wire:model="description" label="Deskripsi (opsional)" placeholder="Deskripsi singkat..." rows="2" />
                <div class="grid grid-cols-2 gap-5">
                    <x-wireui-input wire:model="price" label="Harga (Rp)" type="number" placeholder="0" min="0" />
                    <x-wireui-input wire:model="stockQty" label="Stok" type="number" placeholder="0" min="0" />
                </div>
                <x-wireui-toggle wire:model="isAvailable" label="Item Tersedia" />
            </div>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3">
                    <x-wireui-button flat label="Batal" wire:click="$set('showModal', false)" />
                    <x-wireui-button primary label="Simpan" wire:click="save" wire:loading.attr="disabled" />
                </div>
            </x-slot>
        </x-wireui-card>
    </x-wireui-modal>
</div>
