<div class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Kelola Kategori</h1>
            <p class="text-sm text-gray-500">Manajemen kategori menu makanan & minuman</p>
        </div>
        <x-wireui-button primary label="Tambah Kategori" icon="plus" wire:click="openCreate" />
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <x-wireui-input
            wire:model.live.debounce.300ms="search"
            placeholder="Cari kategori..."
            icon="magnifying-glass"
            class="max-w-xs"
        />
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">No</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ $loop->iteration + $categories->firstItem() - 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="px-5 py-3 text-gray-500 max-w-xs truncate">{{ $category->description ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">{{ $category->menu_items_count }} item</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($category->is_active)
                                <span class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="openEdit({{ $category->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <x-heroicons::outline.pencil-square class="w-4 h-4" />
                                </button>
                                <button wire:click="delete({{ $category->id }})" wire:confirm="Yakin ingin menghapus kategori ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <x-heroicons::outline.trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <x-heroicons::outline.tag class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">Tidak ada kategori ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $categories->links('livewire.custom-pagination') }}
    </div>

    {{-- Modal --}}
    <x-wireui-modal wire:model="showModal" align="center" max-width="2xl" spacing="px-1 py-6 sm:p-6">
        <x-wireui-card :title="$categoryId ? 'Edit Kategori' : 'Tambah Kategori'" padding="p-4 sm:p-6">
            <div class="space-y-5">
                <x-wireui-input wire:model="name" label="Nama Kategori" placeholder="cth: Makanan Utama" />
                <x-wireui-textarea wire:model="description" label="Deskripsi (opsional)" placeholder="Deskripsi singkat kategori..." rows="3" />
                <x-wireui-toggle wire:model="isActive" label="Kategori Aktif" />
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
