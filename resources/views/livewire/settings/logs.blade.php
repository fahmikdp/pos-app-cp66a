<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-800">Log Error Sistem</h1>
                <span class="px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-700 rounded-full">Superadmin Only</span>
            </div>
            <p class="text-sm text-gray-500">Monitoring error aplikasi (storage/logs/laravel.log)</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-xs text-gray-500 bg-white px-3 py-2 rounded-lg shadow-xs border border-gray-100">
                Ukuran Log: <span class="font-semibold text-gray-700">{{ $logFileSize }}</span>
            </div>
            @if ($logExists && $logFileSize !== '0 B')
                <button
                    wire:click="clearLogs"
                    wire:confirm="Apakah kamu yakin ingin mengosongkan file log?"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <x-heroicons::outline.trash class="w-4 h-4" />
                    Kosongkan Log
                </button>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1 max-w-[300px]">
            <x-wireui-input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kata kunci di log..."
                icon="magnifying-glass"
            />
        </div>
        <div class="w-44">
            <x-wireui-select
                wire:model.live="level"
                placeholder="Semua Level"
                :options="$levelOptions"
                option-label="label"
                option-value="value"
                :min-items-for-search="0"
                clearable
            />
        </div>
    </div>

    {{-- Log Entries --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 divide-y divide-gray-100">
        @forelse ($logs as $index => $log)
            @php
                $levelColors = [
                    'ERROR'    => 'bg-red-100 text-red-700 border-red-200',
                    'CRITICAL' => 'bg-red-200 text-red-800 border-red-300',
                    'WARNING'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'INFO'     => 'bg-blue-100 text-blue-700 border-blue-200',
                    'DEBUG'    => 'bg-gray-100 text-gray-700 border-gray-200',
                ];
                $badgeClass = $levelColors[$log['level']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            @endphp
            <div class="p-4 hover:bg-gray-50/80 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 cursor-pointer" wire:click="toggleExpand({{ $index }})">
                    <div class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border shrink-0 {{ $badgeClass }}">
                            {{ $log['level'] }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 break-all leading-tight">{{ $log['message'] }}</p>
                            <span class="text-xs text-gray-400">{{ $log['timestamp'] }} • env: {{ $log['env'] }}</span>
                        </div>
                    </div>
                    @if ($log['context'])
                        <div class="flex items-center gap-1 text-xs text-primary-600 font-medium shrink-0">
                            <span>{{ $expandedIndex === $index ? 'Sembunyikan Trace' : 'Detail Trace' }}</span>
                            <x-heroicons::outline.chevron-down class="w-4 h-4 transition-transform duration-200 {{ $expandedIndex === $index ? 'rotate-180' : '' }}" />
                        </div>
                    @endif
                </div>

                @if ($expandedIndex === $index && $log['context'])
                    <div class="mt-3 p-3 bg-gray-900 text-gray-200 rounded-lg text-xs font-mono overflow-x-auto whitespace-pre-wrap max-h-96 leading-relaxed border border-gray-800">
                        {{ $log['context'] }}
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center text-gray-400">
                <x-heroicons::outline.check-circle class="w-10 h-10 mx-auto mb-2 text-emerald-500" />
                <p class="text-sm font-medium text-gray-600">Tidak ada log error ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Sistem berjalan dengan aman tanpa error aktif.</p>
            </div>
        @endforelse
    </div>

</div>
