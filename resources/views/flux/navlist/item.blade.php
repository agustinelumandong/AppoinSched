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
    ->add('h-10 lg:h-12 mb-1 relative flex items-center gap-3 rounded-lg truncate w-full! mb-2! text-start px-3 my-px')
    ->add($square ? 'px-2.5!' : '')
    ->add('text-zinc-500 transition-all duration-150 ease-in-out')

    // Base hover (different for active vs non-active)
    ->add(match (true) {
        $isActive => 'hover:bg-' . $activeColor . '-600 hover:text-white', // darker hover for active
        default => 'hover:bg-' . $activeColor . '-100 hover:text-' . $activeColor . '-700', // soft hover for inactive
    })

    // Variant and accent logic (kept for consistency)
    ->add(match ($variant) {
        'outline' => match ($accent) {
            true => [
                'border border-transparent',
            ],
            false => [],
        },
        default => match ($accent) {
            true => [],
            false => [],
        },
    })

    // Focus state (solid blue background + white text)
    ->add('focus:outline-none focus:ring-2 focus:ring-' . $activeColor . '-400 focus:bg-' . $activeColor . '-500 focus:text-white')

    // Active pressed state (solid main color)
    ->add('active:bg-' . $activeColor . '-500 active:text-white')

    // Highlight when current or active route
    ->add($isActive ? 'bg-' . $activeColor . '-500 text-white ring-2 ring-' . $activeColor . '-400' : '');


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
