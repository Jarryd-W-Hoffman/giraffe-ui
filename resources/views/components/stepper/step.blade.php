@props([
    'status' => 'upcoming',
    'current' => null,
    'description' => null,
    'href' => null,
])

@php
    $status = in_array($status, ['complete', 'current', 'upcoming', 'error'], true) ? $status : 'upcoming';
    $current ??= $status === 'current';

    // A step with an href is a link, and one with a click handler is a button, so people can go back to it.
    $interactive = $href !== null || $attributes->whereStartsWith(['wire:click', 'x-on:click', '@click'])->filter(fn ($value) => $value !== null && $value !== false)->isNotEmpty();
    $tag = $href !== null ? 'a' : ($interactive ? 'button' : 'div');

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $indicators = [
        'complete' => 'bg-gui-primary text-gui-primary-foreground',
        'current' => 'bg-white text-gui-primary-text ring-2 ring-gui-primary-text ring-inset before:content-[counter(gui-step)] dark:bg-zinc-950',
        'upcoming' => 'bg-white text-zinc-600 ring-1 ring-zinc-300 ring-inset before:content-[counter(gui-step)] dark:bg-zinc-950 dark:text-zinc-400 dark:ring-zinc-700',
        'error' => 'bg-gui-danger text-gui-danger-foreground',
    ];

    $labels = [
        'complete' => 'text-zinc-900 dark:text-zinc-100',
        'current' => 'text-gui-primary-text',
        'upcoming' => 'text-zinc-600 dark:text-zinc-400',
        'error' => 'text-gui-danger-text',
    ];

    // Read out after the label, since the indicator's check mark and colours are hidden from screen readers.
    $statusText = ['complete' => 'completed', 'error' => 'needs attention'][$status] ?? null;
@endphp

<li class="group/step relative flex-1 [counter-increment:gui-step] last:flex-none group-data-[orientation=vertical]/stepper:flex-none group-data-[orientation=vertical]/stepper:pb-8 group-data-[orientation=vertical]/stepper:last:pb-0">
    {{-- The connector runs from this step's indicator towards the next step's. --}}
    <span @class([
        'absolute top-4 right-2 left-10 h-0.5 -translate-y-1/2 rounded-full group-last/step:hidden',
        'group-data-[orientation=vertical]/stepper:top-10 group-data-[orientation=vertical]/stepper:right-auto group-data-[orientation=vertical]/stepper:bottom-2 group-data-[orientation=vertical]/stepper:left-4 group-data-[orientation=vertical]/stepper:h-auto group-data-[orientation=vertical]/stepper:w-0.5 group-data-[orientation=vertical]/stepper:-translate-x-1/2 group-data-[orientation=vertical]/stepper:translate-y-0',
        'bg-gui-primary' => $status === 'complete',
        'bg-zinc-200 dark:bg-zinc-800' => $status !== 'complete',
    ]) aria-hidden="true"></span>

    <{{ $tag }}
        @if ($tag === 'a') href="{{ $href }}" @endif
        @if ($tag === 'button') type="button" @endif
        @if ($current) aria-current="step" @endif
        {{ $attributes->class([
            'group/link flex flex-col items-start gap-2 pr-4 text-left',
            'group-data-[orientation=vertical]/stepper:flex-row group-data-[orientation=vertical]/stepper:gap-3 group-data-[orientation=vertical]/stepper:pr-0',
            'cursor-pointer rounded-gui focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text' => $interactive,
        ]) }}
    >
        <span aria-hidden="true" class="flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-medium tabular-nums {{ $indicators[$status] }}">
            @if ($status === 'complete')
                <x-gui::icon name="check" size="sm" />
            @elseif ($status === 'error')
                !
            @endif
        </span>

        <span class="flex min-w-0 flex-col group-data-[orientation=vertical]/stepper:pt-1.5">
            <span @class([
                'text-sm font-medium break-words',
                $labels[$status],
                'group-hover/link:underline' => $interactive,
            ])>{{ $slot }}@if ($statusText)<span class="sr-only">, {{ $statusText }}</span>@endif</span>

            @if ($description)
                <span class="text-sm text-zinc-600 max-sm:group-data-[orientation=horizontal]/stepper:sr-only dark:text-zinc-400">{{ $description }}</span>
            @endif
        </span>
    </{{ $tag }}>
</li>
