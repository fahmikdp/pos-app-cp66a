<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Manajemen User</h1>
            <p class="text-sm text-gray-500">Kelola akun superadmin, admin, dan kasir</p>
        </div>
        <x-wireui-button primary label="Tambah User" icon="plus" wire:click="openCreate" />
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col sm:flex-row gap-3">
        <x-wireui-input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." icon="magnifying-glass" class="flex-1 max-w-xs" />
        <div class="w-44">
            <x-wireui-select
                wire:model.live="filterRole"
                placeholder="Semua Role"
                :options="$roleOptions"
                option-label="label"
                option-value="value"
                :min-items-for-search="0"
                clearable
            />
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[650px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">No</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $roleColors = ['superadmin' => 'bg-purple-100 text-purple-700', 'admin' => 'bg-blue-100 text-blue-700', 'kasir' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleActive({{ $user->id }})" class="{{ $user->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }} px-2 py-1 text-xs font-medium rounded-full transition-colors">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="openEdit({{ $user->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <x-heroicons::outline.pencil-square class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <x-heroicons::outline.users class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">Tidak ada user ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links('livewire.custom-pagination') }}
    </div>

    {{-- Modal --}}
    <x-wireui-modal wire:model="showModal" align="center" max-width="2xl" spacing="px-1 py-6 sm:p-6">
        <x-wireui-card :title="$userId ? 'Edit User' : 'Tambah User'" padding="p-4 sm:p-6">
            <div class="space-y-5">
                <x-wireui-input wire:model="name" label="Nama Lengkap" placeholder="Nama lengkap..." />
                <x-wireui-input wire:model="email" label="Email" type="email" placeholder="email@pos.test" />
                <x-wireui-input wire:model="password" label="{{ $userId ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}" type="password" placeholder="••••••••" />
                <x-wireui-select
                    wire:model="role"
                    label="Role"
                    :options="$roleOptions"
                    option-label="label"
                    option-value="value"
                    :min-items-for-search="0"
                />
                <x-wireui-toggle wire:model="isActive" label="Akun Aktif" />
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
