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
            'primary' => 'bg-gui-primary text-gui-primary-foreground hover:bg-gui-primary/90 focus-visible:outline-gui-primary-text',
            'secondary' => 'bg-gui-secondary text-gui-secondary-foreground hover:bg-gui-secondary/90 focus-visible:outline-gui-secondary-text',
            'success' => 'bg-gui-success text-gui-success-foreground hover:bg-gui-success/90 focus-visible:outline-gui-success-text',
            'warning' => 'bg-gui-warning text-gui-warning-foreground hover:bg-gui-warning/90 focus-visible:outline-gui-warning-text',
            'danger' => 'bg-gui-danger text-gui-danger-foreground hover:bg-gui-danger/90 focus-visible:outline-gui-danger-text',
            'info' => 'bg-gui-info text-gui-info-foreground hover:bg-gui-info/90 focus-visible:outline-gui-info-text',
        ],
        'outline' => [
            'primary' => 'border border-gui-primary-text text-gui-primary-text hover:bg-gui-primary/10 focus-visible:outline-gui-primary-text',
            'secondary' => 'border border-gui-secondary-text text-gui-secondary-text hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary-text',
            'success' => 'border border-gui-success-text text-gui-success-text hover:bg-gui-success/10 focus-visible:outline-gui-success-text',
            'warning' => 'border border-gui-warning-text text-gui-warning-text hover:bg-gui-warning/10 focus-visible:outline-gui-warning-text',
            'danger' => 'border border-gui-danger-text text-gui-danger-text hover:bg-gui-danger/10 focus-visible:outline-gui-danger-text',
            'info' => 'border border-gui-info-text text-gui-info-text hover:bg-gui-info/10 focus-visible:outline-gui-info-text',
        ],
        'ghost' => [
            'primary' => 'text-gui-primary-text hover:bg-gui-primary/10 focus-visible:outline-gui-primary-text',
            'secondary' => 'text-gui-secondary-text hover:bg-gui-secondary/10 focus-visible:outline-gui-secondary-text',
            'success' => 'text-gui-success-text hover:bg-gui-success/10 focus-visible:outline-gui-success-text',
            'warning' => 'text-gui-warning-text hover:bg-gui-warning/10 focus-visible:outline-gui-warning-text',
            'danger' => 'text-gui-danger-text hover:bg-gui-danger/10 focus-visible:outline-gui-danger-text',
            'info' => 'text-gui-info-text hover:bg-gui-info/10 focus-visible:outline-gui-info-text',
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
