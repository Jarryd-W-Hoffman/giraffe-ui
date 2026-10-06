@props([
    'value' => null,
    'max' => 100,
    'label' => null,
    'color' => 'primary',
    'showValue' => false,
])

@php
    $indeterminate = $value === null;
    $percent = $indeterminate ? null : max(0, min(100, round(((float) $value / max((float) $max, 1)) * 100)));

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $colors = [
        'primary' => 'bg-gui-primary',
        'secondary' => 'bg-gui-secondary',
        'success' => 'bg-gui-success',
        'warning' => 'bg-gui-warning',
        'danger' => 'bg-gui-danger',
        'info' => 'bg-gui-info',
    ];
@endphp

<div {{ $attributes->class('w-full') }}>
    @if ($label || $showValue)
        <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
            @if ($label)
                <span class="font-medium text-zinc-900 dark:text-zinc-100" aria-hidden="true">{{ $label }}</span>
            @endif
            @if ($showValue && ! $indeterminate)
                <span class="ms-auto text-zinc-600 tabular-nums dark:text-zinc-400" aria-hidden="true">{{ $percent }}%</span>
            @endif
        </div>
    @endif

    <div
        role="progressbar"
        @if ($label) aria-label="{{ $label }}" @endif
        aria-valuemin="0"
        aria-valuemax="100"
        @unless ($indeterminate) aria-valuenow="{{ $percent }}" @endunless
        class="relative h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800"
    >
        @if ($indeterminate)
            {{-- Without a value the bar slides back and forth; with reduced motion it stays still. --}}
            <div class="absolute inset-y-0 w-1/3 animate-[gui-indeterminate_1.4s_ease-in-out_infinite] rounded-full motion-reduce:animate-none {{ $colors[$color] ?? $colors['primary'] }}"></div>
        @else
            <div class="h-full rounded-full transition-[width] duration-300 motion-reduce:transition-none {{ $colors[$color] ?? $colors['primary'] }}" style="width: {{ $percent }}%"></div>
        @endif
    </div>
</div>
