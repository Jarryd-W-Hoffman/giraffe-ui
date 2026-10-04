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

    // Heroicons (MIT), 20px solid.
    $icons = [
        'success' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z',
        'warning' => 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z',
        'danger' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z',
        'info' => 'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z',
    ];
    $icons['primary'] = $icons['secondary'] = $icons['info'];
@endphp

<div {{ $attributes->class(['flex gap-3 rounded-gui border p-4 text-sm', $box]) }}>
    @if ($icon instanceof \Illuminate\View\ComponentSlot)
        <div class="shrink-0 {{ $accent }}">{{ $icon }}</div>
    @elseif ($icon)
        <svg class="size-5 shrink-0 {{ $accent }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="{{ $icons[$color] ?? $icons['info'] }}" />
        </svg>
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
