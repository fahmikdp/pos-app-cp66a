@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-5 py-4 bg-white border-t border-gray-100">
        {{-- Information --}}
        <p class="text-sm text-gray-500">
            Menampilkan
            <span class="font-semibold text-gray-800">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-semibold text-gray-800">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-gray-800">{{ $paginator->total() }}</span>
            data
        </p>

        {{-- Pagination Buttons --}}
        <div class="inline-flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed select-none">
                    Sebelumnya
                </span>
            @else
                <button
                    wire:click="previousPage"
                    wire:loading.attr="disabled"
                    class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors shadow-xs"
                >
                    Sebelumnya
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 py-1.5 text-xs font-medium text-gray-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <button
                                wire:click="gotoPage({{ $page }})"
                                wire:loading.attr="disabled"
                                class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors shadow-xs"
                            >
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button
                    wire:click="nextPage"
                    wire:loading.attr="disabled"
                    class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-primary-600 transition-colors shadow-xs"
                >
                    Selanjutnya
                </button>
            @else
                <span class="px-3 py-1.5 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed select-none">
                    Selanjutnya
                </span>
            @endif
        </div>
    </div>
@endif
