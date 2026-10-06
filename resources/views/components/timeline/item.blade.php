@props([
    'title' => null,
    'time' => null,
    'datetime' => null,
    'icon' => null,
    'color' => 'secondary',
    'format' => 'j M Y, H:i',
])

@php
    // A date object becomes readable text, with a machine-readable datetime alongside it.
    if ($time instanceof \DateTimeInterface) {
        $datetime ??= $time->format(DATE_ATOM);
        $time = $time->format($format);
    }

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $icons = [
        'primary' => 'bg-gui-primary text-gui-primary-foreground',
        'secondary' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
        'success' => 'bg-gui-success text-gui-success-foreground',
        'warning' => 'bg-gui-warning text-gui-warning-foreground',
        'danger' => 'bg-gui-danger text-gui-danger-foreground',
        'info' => 'bg-gui-info text-gui-info-foreground',
    ];
    $dots = [
        'primary' => 'bg-gui-primary',
        'secondary' => 'bg-zinc-400 dark:bg-zinc-500',
        'success' => 'bg-gui-success',
        'warning' => 'bg-gui-warning',
        'danger' => 'bg-gui-danger',
        'info' => 'bg-gui-info',
    ];
@endphp

{{--
    One event. The marker is an icon in a circle, a small dot, or your own (the marker slot: an avatar, say). A line
    joins it to the next event. Colour is decoration: say what happened in the title.
--}}
<li {{ $attributes->class('group/item relative flex gap-3 pb-6 last:pb-0') }}>
    {{-- The line down to the next event's marker; the last event has none. --}}
    <span class="absolute top-9 bottom-1 left-4 w-px -translate-x-1/2 bg-zinc-200 group-last/item:hidden dark:bg-zinc-800" aria-hidden="true"></span>

    <div class="relative flex size-8 shrink-0 items-center justify-center" aria-hidden="true">
        @isset($marker)
            {{ $marker }}
        @elseif ($icon)
            <span class="flex size-8 items-center justify-center rounded-full {{ $icons[$color] ?? $icons['secondary'] }}">
                <x-gui::icon :name="$icon" size="sm" />
            </span>
        @else
            <span class="size-2.5 rounded-full {{ $dots[$color] ?? $dots['secondary'] }}"></span>
        @endisset
    </div>

    <div class="min-w-0 flex-1 pt-1.5">
        @if ($title !== null || $time !== null)
            <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-0.5">
                @if ($title !== null)
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $title }}</p>
                @endif
                @if ($time !== null)
                    <time @if ($datetime) datetime="{{ $datetime }}" @endif class="shrink-0 text-xs text-zinc-600 tabular-nums dark:text-zinc-400">{{ $time }}</time>
                @endif
            </div>
        @endif
        @if ($slot->isNotEmpty())
            <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $slot }}</div>
        @endif
    </div>
</li>
