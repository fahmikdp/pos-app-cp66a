<div
    x-data="{ sidebarOpen: true, isMobile: window.innerWidth < 1024 }"
    x-init="
        sidebarOpen = !isMobile;
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 1024;
            sidebarOpen = !isMobile;
        });
        window.addEventListener('toggle-sidebar', () => sidebarOpen = !sidebarOpen);
    "
    class="relative h-full"
>
    {{-- Overlay mobile --}}
    <div
        x-show="sidebarOpen && isMobile"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:leave="transition-opacity ease-in duration-150"
    ></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white flex flex-col h-full transition-transform duration-300 ease-in-out lg:translate-x-0"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 px-5 border-b border-gray-700">
            <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                <x-heroicons::solid.shopping-cart class="w-5 h-5 text-white" />
            </div>
            <span class="text-lg font-bold tracking-wide">Soto Pelajar</span>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            @foreach ($menus as $menu)
                @if ($menu->children->isEmpty())
                    {{-- Single menu --}}
                    <a
                        href="{{ $menu->route ? route($menu->route) : '#' }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium transition-colors
                            {{ request()->routeIs($menu->route) ? 'bg-primary-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                    >
                        @if($menu->icon)
                            <x-dynamic-component :component="'heroicons::outline.' . $menu->icon" class="w-5 h-5 flex-shrink-0" />
                        @endif
                        {{ $menu->label }}
                    </a>
                @else
                    {{-- Group dengan submenu --}}
                    <div
                        x-data="{
                            open: {{ request()->routeIs($menu->name . '.*') ? 'true' : 'false' }}
                        }"
                        class="mb-1"
                    >
                        <button
                            @click="open = !open"
                            class="flex items-center justify-between w-full gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs($menu->name . '.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}"
                        >
                            <div class="flex items-center gap-3">
                                @if($menu->icon)
                                    <x-dynamic-component :component="'heroicons::outline.' . $menu->icon" class="w-5 h-5 flex-shrink-0" />
                                @endif
                                {{ $menu->label }}
                            </div>
                            <x-heroicons::outline.chevron-right
                                class="w-4 h-4 transition-transform duration-200"
                                ::class="open ? 'rotate-90' : ''"
                            />
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="ml-4 mt-1 space-y-0.5"
                        >
                            @foreach ($menu->children as $child)
                                <a
                                    href="{{ $child->route ? route($child->route) : '#' }}"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                                        {{ request()->routeIs($child->route) ? 'bg-primary-600 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                                >
                                    @if($child->icon)
                                        <x-dynamic-component :component="'heroicons::outline.' . $child->icon" class="w-4 h-4 flex-shrink-0" />
                                    @endif
                                    {{ $child->label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        {{-- Bottom user info --}}
        <div class="border-t border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
        </div>
    </aside>
</div>
