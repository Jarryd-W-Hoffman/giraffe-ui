@props([
    'color' => 'info',
    'title' => null,
    'icon' => true,
])

@php
    // Every class is written out in full so Tailwind can find it when scanning this file.
    $colors = [
        'primary' => ['border-gui-primary-text/30 bg-gui-primary/5', 'text-gui-primary-text'],
        'secondary' => ['border-gui-secondary-text/30 bg-gui-secondary/5', 'text-gui-secondary-text'],
        'success' => ['border-gui-success-text/30 bg-gui-success/5', 'text-gui-success-text'],
        'warning' => ['border-gui-warning-text/30 bg-gui-warning/5', 'text-gui-warning-text'],
        'danger' => ['border-gui-danger-text/30 bg-gui-danger/5', 'text-gui-danger-text'],
        'info' => ['border-gui-info-text/30 bg-gui-info/5', 'text-gui-info-text'],
    ];

    [$box, $accent] = $colors[$color] ?? $colors['info'];

    $iconName = [
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'x-circle',
    ][$color] ?? 'information-circle';
@endphp

<div {{ $attributes->class(['flex gap-3 rounded-gui border p-4 text-sm', $box]) }}>
    @if ($icon instanceof \Illuminate\View\ComponentSlot)
        <div class="shrink-0 {{ $accent }}">{{ $icon }}</div>
    @elseif ($icon)
        <x-gui::icon :name="$iconName" class="{{ $accent }}" />
    @endif

    <div class="min-w-0 flex-1 space-y-1">
        @if ($title)
            <p class="font-medium {{ $accent }}">{{ $title }}</p>
        @endif
        @if (! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot))
            <div class="text-zinc-700 dark:text-zinc-300">{{ $slot }}</div>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-start gap-2">{{ $actions }}</div>
    @endisset
</div>
