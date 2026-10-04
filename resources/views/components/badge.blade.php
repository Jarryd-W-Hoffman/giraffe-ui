@props([
    'variant' => 'soft',
    'color' => 'secondary',
    'size' => 'md',
])

@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    $variants = [
        'soft' => [
            'primary' => 'bg-gui-primary/10 text-gui-primary-text ring-gui-primary-text/25',
            'secondary' => 'bg-gui-secondary/10 text-gui-secondary-text ring-gui-secondary-text/25',
            'success' => 'bg-gui-success/10 text-gui-success-text ring-gui-success-text/25',
            'warning' => 'bg-gui-warning/10 text-gui-warning-text ring-gui-warning-text/25',
            'danger' => 'bg-gui-danger/10 text-gui-danger-text ring-gui-danger-text/25',
            'info' => 'bg-gui-info/10 text-gui-info-text ring-gui-info-text/25',
        ],
        'solid' => [
            'primary' => 'bg-gui-primary text-gui-primary-foreground ring-transparent',
            'secondary' => 'bg-gui-secondary text-gui-secondary-foreground ring-transparent',
            'success' => 'bg-gui-success text-gui-success-foreground ring-transparent',
            'warning' => 'bg-gui-warning text-gui-warning-foreground ring-transparent',
            'danger' => 'bg-gui-danger text-gui-danger-foreground ring-transparent',
            'info' => 'bg-gui-info text-gui-info-foreground ring-transparent',
        ],
        'outline' => [
            'primary' => 'text-gui-primary-text ring-gui-primary-text',
            'secondary' => 'text-gui-secondary-text ring-gui-secondary-text',
            'success' => 'text-gui-success-text ring-gui-success-text',
            'warning' => 'text-gui-warning-text ring-gui-warning-text',
            'danger' => 'text-gui-danger-text ring-gui-danger-text',
            'info' => 'text-gui-info-text ring-gui-info-text',
        ],
    ];

    $sizes = [
        'sm' => 'px-1.5 py-0.5 text-xs gap-1',
        'md' => 'px-2 py-0.5 text-sm gap-1.5',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex w-fit shrink-0 items-center rounded-gui font-medium whitespace-nowrap ring-1 ring-inset',
    ($variants[$variant] ?? $variants['soft'])[$color] ?? $variants['soft']['secondary'],
    $sizes[$size] ?? $sizes['md'],
]) }}>{{ $slot }}</span>
