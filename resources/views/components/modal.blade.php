@props([
    'id',
    'title' => 'Modal Title',
    'backdrop' => true,
    'closable' => true,
    'size' => 'max-w-2xl', // Tailwind size: sm, md, lg, xl, etc.
    'footerClass' => 'justify-end',
])

<div
    x-data="{ open: false }"
    x-show="open"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 overflow-y-auto"
    x-cloak
    x-transition:enter="ease-out duration-100"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    @if ($backdrop)
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-on:click="open = false"></div>
    @endif

    <div class="flex min-h-screen items-center justify-center p-4">
        <div
            class="relative w-full {{ $size }} transform overflow-hidden rounded-lg bg-white shadow-xl transition-all"
            x-transition:enter="ease-out duration-100"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <!-- Header -->
            <div class="border-b px-6 py-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $title }}
                    </h3>
                    @if ($closable)
                        <button
                            type="button"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none"
                            x-on:click="open = false"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 text-gray-700">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @isset($footer)
                <div class="border-t px-6 py-4">
                    <div class="flex {{ $footerClass }} space-x-3">
                        {{ $footer }}
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>
