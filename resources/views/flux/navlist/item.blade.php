@php $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@aware(['variant'])

@props([
    'iconVariant' => 'outline',
    'iconTrailing' => null,
    'badgeColor' => null,
    'variant' => null,
    'iconDot' => null,
    'accent' => true,
    'badge' => null,
    'icon' => null,
    'route' => null, // New: route name for auto-active state
    'activeColor' => 'blue', // New: accent color for active/focus states
])

@php
    // Check if route matches current page
    $isActive = $route ? request()->routeIs($route) : ($attributes->get('current') ?? false);

    // Button should be a square if it has no text contents
    $square ??= $slot->isEmpty();

    // Icon sizing
    $iconClasses = Flux::classes($square ? 'size-5!' : 'size-4!');

    // Base classes with hover, focus, and active logic
    $classes = Flux::classes()
        ->add('h-10 lg:h-8 relative flex items-center gap-3 rounded-lg truncate w-full! mb-2! text-start px-3 my-px')
        ->add($square ? 'px-2.5!' : '')
        ->add('text-zinc-500 dark:text-white/80 transition-all duration-150 ease-in-out')
        ->add(match ($variant) {
            'outline' => match ($accent) {
                true => [
                    'hover:bg-' . $activeColor . '-50 hover:text-' . $activeColor . '-700',
                    'border border-transparent',
                ],
                false => [
                    'hover:text-zinc-800 dark:hover:text-white',
                ],
            },
            default => match ($accent) {
                true => [
                    'hover:bg-' . $activeColor . '-50 hover:text-' . $activeColor . '-700',
                ],
                false => [
                    'hover:text-zinc-800 dark:hover:text-white',
                ],
            },
        })
        // Focus state
        ->add('focus:outline-none focus:ring-2 focus:ring-' . $activeColor . '-400 focus:bg-' . $activeColor . '-100 dark:focus:ring-' . $activeColor . '-500')
        // Active pressed state
        ->add('active:bg-' . $activeColor . '-200 active:text-' . $activeColor . '-800')
        // Highlight when current or active
        ->add($isActive ? 'bg-' . $activeColor . '-100 text-' . $activeColor . '-700 ring-2 ring-' . $activeColor . '-300 dark:bg-' . $activeColor . '-900/40' : '');
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navlist-item>
    {{-- Leading icon --}}
    @if ($icon)
        <div class="relative">
            @if (is_string($icon) && $icon !== '')
                <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
            @else
                {{ $icon }}
            @endif

            @if ($iconDot)
                <div class="absolute top-[-2px] end-[-2px]">
                    <div class="size-[6px] rounded-full bg-zinc-500 dark:bg-zinc-400"></div>
                </div>
            @endif
        </div>
    @endif

    {{-- Label --}}
    @if ($slot->isNotEmpty())
        <div class="flex-1 text-sm font-medium leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block" data-content>
            {{ $slot }}
        </div>
    @endif

    {{-- Trailing icon --}}
    @if (is_string($iconTrailing) && $iconTrailing !== '')
        <flux:icon :icon="$iconTrailing" :variant="$iconVariant" class="size-4!" />
    @elseif ($iconTrailing)
        {{ $iconTrailing }}
    @endif

    {{-- Badge --}}
    @if (isset($badge) && $badge !== '')
        <flux:navlist.badge :attributes="Flux::attributesAfter('badge:', $attributes, ['color' => $badgeColor])">
            {{ $badge }}
        </flux:navlist.badge>
    @endif
</flux:button-or-link>
