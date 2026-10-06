@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'data' => [],
    'colors' => [],
    'size' => 160,
    'thickness' => 14,
    'center' => null,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
])

@php
    // name => value. Past eight parts, the rest fold into "Other", so a colour is never reused.
    $parts = collect($data)->filter(fn ($value) => is_numeric($value) && $value > 0)->map(fn ($value) => (float) $value);
    if ($parts->count() > 8) {
        $parts = $parts->take(7)->put(__('giraffeui::ui.other'), $parts->slice(7)->sum());
    }
    $total = $parts->sum();
    $format = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals, $compact);
    $exact = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals);
    $percent = fn ($value) => ($share = $value / $total * 100) < 1 ? '<1%' : round($share).'%';

    // Each part's arc, measured in hundredths of the circle, with a 2px-ish gap of surface between neighbours.
    $gap = $parts->count() > 1 ? 0.6 : 0;
    $start = 0;
    $slot = 0;
    $segments = $parts->map(function ($value, $name) use (&$start, &$slot, $total, $gap, $colors, $format, $exact, $percent) {
        $length = $value / $total * 100;
        $segment = [
            'name' => (string) $name,
            'color' => 'var(--color-gui-'.($colors[$name] ?? 'chart-'.(($slot++ % 8) + 1)).')',
            'start' => $start,
            'length' => max(0.01, $length - $gap),
            'value' => $format($value),
            'exact' => $exact($value),
            'percent' => $percent($value),
        ];
        $start += $length;

        return $segment;
    })->values()->all();

    $label ??= collect($segments)->map(fn ($segment) => $segment['name'].' '.$segment['percent'])->join(', ');
    $radius = 50 - $thickness / 2;
@endphp

{{--
    A donut chart for a part-to-whole with a few parts. Arcs are SVG; every label is HTML beside it. The list doubles as
    the legend and the text equivalent: each part's value and share, in your order (sort it largest first). Hover a part
    or a row, or focus the chart and use the arrow keys, to show that part in the centre.
--}}
<figure {{ $attributes->class('@container min-w-0 text-xs text-zinc-600 dark:text-zinc-400') }}>
    @if ($total <= 0)
        <p class="flex items-center justify-center rounded-gui border border-dashed border-zinc-200 text-sm dark:border-zinc-800" style="height: {{ $size }}px">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        <div
            x-data="{
                index: null,
                count: {{ count($segments) }},
                readouts: @js(collect($segments)->map(fn ($segment) => $segment['name'].': '.__('giraffeui::ui.share', ['value' => $segment['exact'], 'percent' => $segment['percent']]))->all()),
                key(event) {
                    const last = this.count - 1;
                    const moves = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
                    if (event.key in moves) this.index = Math.max(0, Math.min(last, (this.index ?? (moves[event.key] > 0 ? -1 : last + 1)) + moves[event.key]));
                    else if (event.key === 'Home') this.index = 0;
                    else if (event.key === 'End') this.index = last;
                    else if (event.key === 'Escape') this.index = null;
                    else return;
                    event.preventDefault();
                },
            }"
            tabindex="0"
            role="group"
            aria-roledescription="chart"
            aria-label="{{ $label }}"
            x-on:keydown="key($event)"
            x-on:blur="index = null"
            x-on:pointerleave="if (document.activeElement !== $el) index = null"
            class="flex flex-col items-center gap-6 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text @sm:flex-row @sm:items-center"
        >
            <div class="relative shrink-0" style="width: {{ $size }}px; height: {{ $size }}px">
                <svg viewBox="0 0 100 100" class="size-full -rotate-90" aria-hidden="true">
                    @foreach ($segments as $index => $segment)
                        <circle
                            cx="50" cy="50" r="{{ $radius }}" pathLength="100" fill="none"
                            stroke="{{ $segment['color'] }}" stroke-width="{{ $thickness }}"
                            stroke-dasharray="{{ $segment['length'] }} {{ 100 - $segment['length'] }}"
                            stroke-dashoffset="{{ -$segment['start'] }}"
                            class="transition-opacity duration-150"
                            x-bind:class="index !== null && index !== {{ $index }} && 'opacity-30'"
                            x-on:pointerenter="index = {{ $index }}"
                        />
                    @endforeach
                </svg>
                {{-- The centre: the total, or the part being pointed at. --}}
                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center" aria-hidden="true">
                    <div x-show="index === null">
                        <p class="text-lg font-semibold text-zinc-900 tabular-nums dark:text-zinc-100">{{ $center ?? $format($total) }}</p>
                        <p>{{ __('giraffeui::ui.total') }}</p>
                    </div>
                    @foreach ($segments as $index => $segment)
                        <div x-show="index === {{ $index }}" x-cloak class="max-w-[70%]">
                            <p class="text-lg font-semibold text-zinc-900 tabular-nums dark:text-zinc-100">{{ $segment['percent'] }}</p>
                            <p class="truncate">{{ $segment['name'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- The legend and the numbers in one: name, value, share. Screen readers read this list. --}}
            <ul class="w-full min-w-0 flex-1 space-y-0.5 @sm:max-w-xs">
                @foreach ($segments as $index => $segment)
                    <li
                        class="flex items-center gap-2 rounded-sm px-1.5 py-1"
                        x-bind:class="index === {{ $index }} && 'bg-zinc-100 dark:bg-zinc-800/70'"
                        x-on:pointerenter="index = {{ $index }}"
                    >
                        <span class="size-2.5 shrink-0 rounded-[2px]" style="background: {{ $segment['color'] }}" aria-hidden="true"></span>
                        <span class="min-w-0 flex-1 truncate text-zinc-700 dark:text-zinc-300">{{ $segment['name'] }}</span>
                        <span class="sr-only">{{ __('giraffeui::ui.share', ['value' => $segment['exact'], 'percent' => $segment['percent']]) }}</span>
                        <span class="font-medium text-zinc-900 tabular-nums dark:text-zinc-100" aria-hidden="true">{{ $segment['value'] }}</span>
                        <span class="w-10 text-right tabular-nums" aria-hidden="true">{{ $segment['percent'] }}</span>
                    </li>
                @endforeach
            </ul>

            <p class="sr-only" aria-live="polite" x-text="index === null ? '' : readouts[index]"></p>
        </div>
    @endif
</figure>
