@props([
    'variant' => 'solid',
    'color' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'external' => false,
])

@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    $variants = [
        'solid' => [
            'primary' => 'bg-gui-primary text-gui-primary-foreground hover:bg-gui-primary/90 focus-visible:outline-gui-primary',
            'secondary' => 'bg-gui-secondary text-gui-secondary-foreground hover:bg-gui-secondary/90 focus-visible:outline-gui-secondary',
            'success' => 'bg-gui-success text-gui-success-foreground hover:bg-gui-success/90 focus-visible:outline-gui-success',
            'warning' => 'bg-gui-warning text-gui-warning-foreground hover:bg-gui-warning/90 focus-visible:outline-gui-warning',
            'danger' => 'bg-gui-danger text-gui-danger-foreground hover:bg-gui-danger/90 focus-visible:outline-gui-danger',
            'info' => 'bg-gui-info text-gui-info-foreground hover:bg-gui-info/90 focus-visible:outline-gui-info',
        ],
        'outline' => [
            'primary' => 'border border-gui-primary text-gui-primary hover:bg-gui-primary/10 focus-visible:outline-gui-primary',
            'secondary' => 'border border-gui-secondary text-gui-secondary hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary',
            'success' => 'border border-gui-success text-gui-success hover:bg-gui-success/10 focus-visible:outline-gui-success',
            'warning' => 'border border-gui-warning text-gui-warning hover:bg-gui-warning/10 focus-visible:outline-gui-warning',
            'danger' => 'border border-gui-danger text-gui-danger hover:bg-gui-danger/10 focus-visible:outline-gui-danger',
            'info' => 'border border-gui-info text-gui-info hover:bg-gui-info/10 focus-visible:outline-gui-info',
        ],
        'ghost' => [
            'primary' => 'text-gui-primary hover:bg-gui-primary/10 focus-visible:outline-gui-primary',
            'secondary' => 'text-gui-secondary hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary',
            'success' => 'text-gui-success hover:bg-gui-success/10 focus-visible:outline-gui-success',
            'warning' => 'text-gui-warning hover:bg-gui-warning/10 focus-visible:outline-gui-warning',
            'danger' => 'text-gui-danger hover:bg-gui-danger/10 focus-visible:outline-gui-danger',
            'info' => 'text-gui-info hover:bg-gui-info/10 focus-visible:outline-gui-info',
        ],
    ];

    $sizes = [
        'sm' => 'h-8 px-3 gap-1.5 text-sm',
        'md' => 'h-10 px-4 gap-2 text-sm',
        'lg' => 'h-12 px-6 gap-2.5 text-base',
    ];

    $classes = implode(' ', [
        'inline-flex shrink-0 items-center justify-center rounded-gui font-medium whitespace-nowrap transition-colors',
        'cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'aria-disabled:pointer-events-none aria-disabled:opacity-50',
        ($variants[$variant] ?? $variants['solid'])[$color] ?? $variants['solid']['primary'],
        $sizes[$size] ?? $sizes['md'],
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
        @disabled($disabled)
        {{ $attributes->class($classes) }}
    >
        {{ $leading ?? '' }}
        {{ $slot }}
        {{ $trailing ?? '' }}
    </button>
@endif
