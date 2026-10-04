<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }} - Warung Soto Pelajar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <x-wireui-notifications />
    <div class="flex h-screen overflow-hidden">
        @livewire('layout.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">
            {{-- Top Navbar --}}
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center gap-3">
                        <button
                            x-data
                            @click="window.dispatchEvent(new Event('toggle-sidebar'))"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden"
                        >
                            <x-heroicons::outline.bars-3 class="w-6 h-6" />
                        </button>
                        @php $breadcrumbs = \App\Models\Menu::getBreadcrumbs(); @endphp
                        @if (count($breadcrumbs) > 0)
                            <nav class="flex items-center gap-2 text-sm">
                                @foreach ($breadcrumbs as $index => $crumb)
                                    @if ($index > 0)
                                        <x-heroicons::outline.chevron-right class="w-3.5 h-3.5 text-gray-400" />
                                    @endif
                                    @if ($loop->last)
                                        <span class="font-semibold text-gray-800">{{ $crumb['label'] }}</span>
                                    @else
                                        <span class="text-gray-500">{{ $crumb['label'] }}</span>
                                    @endif
                                @endforeach
                            </nav>
                        @else
                            <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <div
                            x-data="{
                                dateTime: '',
                                updateClock() {
                                    const now = new Date();
                                    const dateStr = now.toLocaleDateString('id-ID', {
                                        weekday: 'long',
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                    const timeStr = now.toLocaleTimeString('id-ID', {
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: false
                                    }).replace(/\./g, ':');
                                    this.dateTime = `${dateStr} • ${timeStr} WIB`;
                                }
                            }"
                            x-init="updateClock(); setInterval(() => updateClock(), 1000)"
                            class="hidden sm:flex items-center gap-2 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm"
                        >
                            <x-heroicons::outline.clock class="w-4 h-4 text-primary-600 flex-shrink-0" />
                            <span x-text="dateTime"></span>
                        </div>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold text-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                <x-heroicons::outline.chevron-down class="w-4 h-4" />
                            </button>

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50"
                            >
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <x-heroicons::outline.arrow-right-on-rectangle class="w-4 h-4" />
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</body>
</html>
