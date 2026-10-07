@props([
    'variant' => 'solid',
    'color' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'external' => false,
    'loading' => false,
    'shape' => 'default',
    'icon' => false,
])

@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    $variants = [
        'solid' => [
            'primary' => 'bg-gui-primary text-gui-primary-foreground hover:bg-gui-primary/90 focus-visible:outline-gui-primary-text',
            'secondary' => 'bg-gui-secondary text-gui-secondary-foreground hover:bg-gui-secondary/90 focus-visible:outline-gui-secondary-text',
            'success' => 'bg-gui-success text-gui-success-foreground hover:bg-gui-success/90 focus-visible:outline-gui-success-text',
            'warning' => 'bg-gui-warning text-gui-warning-foreground hover:bg-gui-warning/90 focus-visible:outline-gui-warning-text',
            'danger' => 'bg-gui-danger text-gui-danger-foreground hover:bg-gui-danger/90 focus-visible:outline-gui-danger-text',
            'info' => 'bg-gui-info text-gui-info-foreground hover:bg-gui-info/90 focus-visible:outline-gui-info-text',
            // ink: the page's own contrast, black on light and white on dark.
            'ink' => 'bg-zinc-900 text-white hover:bg-zinc-700 focus-visible:outline-zinc-900 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 dark:focus-visible:outline-white',
        ],
        'outline' => [
            'primary' => 'border border-gui-primary-text text-gui-primary-text hover:bg-gui-primary/10 focus-visible:outline-gui-primary-text',
            'secondary' => 'border border-gui-secondary-text text-gui-secondary-text hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary-text',
            'success' => 'border border-gui-success-text text-gui-success-text hover:bg-gui-success/10 focus-visible:outline-gui-success-text',
            'warning' => 'border border-gui-warning-text text-gui-warning-text hover:bg-gui-warning/10 focus-visible:outline-gui-warning-text',
            'danger' => 'border border-gui-danger-text text-gui-danger-text hover:bg-gui-danger/10 focus-visible:outline-gui-danger-text',
            'info' => 'border border-gui-info-text text-gui-info-text hover:bg-gui-info/10 focus-visible:outline-gui-info-text',
            'ink' => 'border border-zinc-300 text-zinc-900 hover:bg-zinc-100 focus-visible:outline-zinc-900 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-900 dark:focus-visible:outline-white',
        ],
        'ghost' => [
            'primary' => 'text-gui-primary-text hover:bg-gui-primary/10 focus-visible:outline-gui-primary-text',
            'secondary' => 'text-gui-secondary-text hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary-text',
            'success' => 'text-gui-success-text hover:bg-gui-success/10 focus-visible:outline-gui-success-text',
            'warning' => 'text-gui-warning-text hover:bg-gui-warning/10 focus-visible:outline-gui-warning-text',
            'danger' => 'text-gui-danger-text hover:bg-gui-danger/10 focus-visible:outline-gui-danger-text',
            'info' => 'text-gui-info-text hover:bg-gui-info/10 focus-visible:outline-gui-info-text',
            // On a filled card, a step darker so it still stands out.
            'ink' => 'bg-zinc-100 text-zinc-800 hover:bg-zinc-200 hover:text-zinc-900 focus-visible:outline-zinc-900 in-data-[variant=filled]:bg-zinc-200 in-data-[variant=filled]:hover:bg-zinc-300 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 dark:hover:text-zinc-100 dark:focus-visible:outline-white dark:in-data-[variant=filled]:bg-zinc-800 dark:in-data-[variant=filled]:hover:bg-zinc-700',
        ],
    ];

    $sizes = [
        'sm' => 'h-8 px-3 gap-1.5 text-sm',
        'md' => 'h-10 px-4 gap-2 text-sm',
        'lg' => 'h-12 px-6 gap-2.5 text-base',
    ];
    // icon: a square button holding just an icon. Give it an aria-label.
    $iconSizes = ['sm' => 'size-8', 'md' => 'size-10', 'lg' => 'size-12'];

    // Only buttons that can be busy carry a spinner: Livewire actions, submit buttons, or loading set by hand.
    $canLoad = $loading || $type === 'submit' || collect($attributes->getAttributes())->keys()->contains(fn ($key) => str_starts_with($key, 'wire:'));

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex shrink-0 items-center justify-center font-medium whitespace-nowrap transition-colors',
        // pill: fully rounded ends, whatever the theme's radius (a circle, with icon).
        $shape === 'pill' ? 'rounded-full' : 'rounded-gui',
        'cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'aria-disabled:pointer-events-none aria-disabled:opacity-50',
        // Livewire 4 sets data-loading on the element that triggered a request.
        'group/button data-loading:pointer-events-none',
        // A submit button is also busy while its wire:submit form is.
        'in-data-loading:pointer-events-none' => $type === 'submit' && ! $href,
        ($variants[$variant] ?? $variants['solid'])[$color] ?? $variants['solid']['primary'],
        $icon ? ($iconSizes[$size] ?? $iconSizes['md']) : ($sizes[$size] ?? $sizes['md']),
    ]);
@endphp

@if ($href)
    <a
        @unless ($disabled) href="{{ $href }}" @endunless
        @if ($disabled) role="link" aria-disabled="true" @endif
        @if ($external) target="_blank" rel="noopener noreferrer" @endif
        {{ $attributes->class($classes) }}
    >
        {{ $leading ?? '' }}
        {{ $slot }}
        {{ $trailing ?? '' }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($disabled || $loading)
        @if ($loading) data-loading="true" aria-busy="true" @endif
        {{ $attributes->class($classes) }}
    >
        @if ($canLoad)
            {{-- Shown and hidden on a wrapper, so these display classes don't fight the spinner's own. --}}
            <span @class([
                'hidden group-data-loading/button:contents',
                'in-data-loading:contents' => $type === 'submit',
            ])><x-gui::spinner size="sm" /></span>
        @endif
        @isset($leading)
            <span @class([
                'contents group-data-loading/button:hidden',
                'in-data-loading:hidden' => $type === 'submit',
            ])>{{ $leading }}</span>
        @endisset
        {{ $slot }}
        {{ $trailing ?? '' }}
    </button>
@endif
