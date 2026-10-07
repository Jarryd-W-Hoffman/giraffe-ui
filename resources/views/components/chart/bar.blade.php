@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'labels' => [],
    'series' => null,
    'data' => null,
    'name' => null,
    'colors' => [],
    'stacked' => false,
    'horizontal' => false,
    'values' => null,
    'height' => 240,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
    'table' => false,
])

@php
    $labels = array_values(collect($labels)->map(fn ($label) => (string) $label)->all());
    $series = collect($series ?? [($name ?? '') => $data ?? []])->map(fn ($values) => array_values(collect($values)->all()))->all();
    $count = count($labels);
    $format = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals, $compact);
    $exact = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals);
    $all = collect($series)->flatten()->all();
    $hasData = $count > 0 && collect($all)->contains(fn ($value) => is_numeric($value));
    $several = count($series) > 1;
    $height = max(80, (int) $height);

    if ($hasData) {
        // Bars always start at zero. Stacks are measured by their totals (positives up, negatives down).
        $extents = $all;
        if ($stacked) {
            $extents = [];
            foreach (range(0, $count - 1) as $index) {
                $column = collect($series)->map(fn ($values) => $values[$index] ?? null)->filter(fn ($value) => is_numeric($value));
                $extents[] = $column->filter(fn ($value) => $value > 0)->sum();
                $extents[] = $column->filter(fn ($value) => $value < 0)->sum();
            }
        }
        ['min' => $min, 'max' => $max, 'ticks' => $ticks] = Chart::ticks($extents, 0.0, Chart::tickCount($height));
        $tickDecimals = $decimals ?? Chart::decimals($ticks);
        $tick = fn ($value) => Chart::format($value, $prefix, $suffix, $tickDecimals, $compact);
        // Where zero sits, as a percentage from the start of the value axis (bottom, or left when horizontal).
        $span = $max - $min;
        $zeroAt = (0 - $min) / $span * 100;
        $percent = fn ($value) => abs($value) / $span * 100;

        $slot = 0;
        $bars = collect($series)->map(function ($values, $seriesName) use (&$slot, $colors, $count, $format, $exact) {
            return [
                'name' => (string) $seriesName,
                'color' => 'var(--color-gui-'.($colors[$seriesName] ?? 'chart-'.(($slot++ % 8) + 1)).')',
                'raw' => array_pad($values, $count, null),
                'values' => array_map(fn ($value) => $format($value), array_pad($values, $count, null)),
                'exact' => array_map(fn ($value) => $exact($value), array_pad($values, $count, null)),
            ];
        })->values()->all();

        // Each category's stack, built from zero outwards, with the outermost segment on each side rounded.
        $stacks = [];
        if ($stacked) {
            foreach (range(0, $count - 1) as $index) {
                $up = $down = 0;
                $segments = [];
                foreach ($bars as $bar) {
                    $value = $bar['raw'][$index];
                    if (! is_numeric($value) || $value == 0) {
                        continue;
                    }
                    $from = $value > 0 ? $up : $down;
                    $value > 0 ? $up += $value : $down += $value;
                    $segments[] = ['color' => $bar['color'], 'start' => $zeroAt + ($value > 0 ? $from : $from + $value) / $span * 100, 'size' => $percent($value), 'negative' => $value < 0];
                }
                // Only the segment furthest from zero on each side gets the rounded data end.
                $lastUp = collect($segments)->filter(fn ($segment) => ! $segment['negative'])->keys()->last();
                $lastDown = collect($segments)->filter(fn ($segment) => $segment['negative'])->keys()->last();
                foreach ($segments as $key => &$segment) {
                    $segment['rounded'] = $key === $lastUp || $key === $lastDown;
                }
                unset($segment);
                $stacks[] = ['segments' => $segments, 'total' => $up + $down];
            }
        }

        // Value labels at the bar ends: on by default for a single, unstacked series that isn't crowded.
        $values ??= ! $several && $count <= 12;

        $label ??= __('giraffeui::ui.chart_summary', [
            'series' => collect($bars)->pluck('name')->filter()->join(', ', ' & ') ?: '',
            'from' => $labels[0],
            'to' => $labels[$count - 1],
        ]);
    }
@endphp

{{--
    A bar chart drawn as HTML: thin bars (at most 24px) with a rounded data end and a square base on the zero line,
    a 2px gap between touching bars and stacked segments, and real text for every label. Hover or focus it and use the
    arrow keys to step through the categories; "Show the data" opens the numbers as a table.
--}}
<figure {{ $attributes->class('@container min-w-0 text-xs text-zinc-600 dark:text-zinc-400') }}>
    @if (! $hasData)
        <p class="flex items-center justify-center rounded-gui border border-dashed border-zinc-200 text-sm dark:border-zinc-800" style="height: {{ $height }}px">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        @if ($several)
            <x-gui::chart.legend :items="$bars" shape="square" class="mb-3" />
        @endif

        <div
            x-data="{
                index: null,
                chart: { labels: [], series: [] },
                init() {
                    this.chart = JSON.parse(this.$refs.data.textContent);
                },
                key(event) {
                    const last = this.chart.labels.length - 1;
                    const moves = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
                    if (event.key in moves) this.index = Math.max(0, Math.min(last, (this.index ?? (moves[event.key] > 0 ? -1 : last + 1)) + moves[event.key]));
                    else if (event.key === 'Home') this.index = 0;
                    else if (event.key === 'End') this.index = last;
                    else if (event.key === 'Escape') this.index = null;
                    else return;
                    event.preventDefault();
                },
                get readout() {
                    if (this.index === null) return '';
                    return this.chart.labels[this.index] + ': ' + this.chart.series.map((bar) => (bar.name ? bar.name + ' ' : '') + bar.values[this.index]).join(', ');
                },
            }"
        >
            <script type="application/json" x-ref="data">{!! json_encode(['labels' => $labels, 'series' => collect($bars)->map(fn ($bar) => ['name' => $bar['name'], 'color' => $bar['color'], 'values' => $bar['values']])->all()], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

            @if ($horizontal)
                {{-- Horizontal: categories down the side, bars growing from zero towards the right (left if negative). --}}
                <div
                    tabindex="0"
                    role="group"
                    aria-roledescription="chart"
                    aria-label="{{ $label }}"
                    x-on:keydown="key($event)"
                    x-on:blur="index = null"
                    x-on:pointerleave="if (document.activeElement !== $el) index = null"
                    class="relative grid grid-cols-[auto_1fr] items-center gap-x-3 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text"
                >
                    @foreach ($labels as $index => $text)
                        <span class="truncate py-1.5 text-right text-zinc-700 dark:text-zinc-300" aria-hidden="true">{{ $text }}</span>
                        <div class="relative" x-on:pointerenter="index = {{ $index }}" aria-hidden="true">
                            {{-- The row's hover wash: the whole row is the target, not just the bar. --}}
                            <div class="absolute -inset-y-0.5 -right-1 -left-1 rounded-sm" x-bind:class="index === {{ $index }} && 'bg-zinc-100 dark:bg-zinc-800/70'"></div>
                            {{-- The row's tooltip, drawn on the server: only when the bars don't already say their values. --}}
                            @if ($several || ! $values)
                            <div x-show="index === {{ $index }}" x-cloak class="pointer-events-none absolute right-0 bottom-full z-10 mb-1 min-w-32 rounded-gui border border-zinc-200 bg-white px-3 py-2 text-xs shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                                <p class="mb-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $text }}</p>
                                @foreach ($bars as $bar)
                                    <p class="flex items-center gap-2 whitespace-nowrap">
                                        <span class="size-2.5 shrink-0 rounded-[2px]" style="background: {{ $bar['color'] }}"></span>
                                        <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100">{{ $bar['values'][$index] }}</span>
                                        @if ($bar['name'] !== '')
                                            <span>{{ $bar['name'] }}</span>
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                            @endif
                            <div class="relative flex min-h-6 flex-col justify-center gap-0.5 py-1 pr-12">
                                <div class="pointer-events-none absolute inset-y-0 w-px bg-zinc-300 dark:bg-zinc-700" style="left: {{ $zeroAt }}%"></div>
                                @if ($stacked)
                                    <div class="relative h-4 max-h-6">
                                        @foreach ($stacks[$index]['segments'] as $segment)
                                            <span @class(['absolute inset-y-0 border-x border-(--gui-chart-surface)', $segment['negative'] ? 'rounded-l' : 'rounded-r' => $segment['rounded']]) style="left: {{ $segment['start'] }}%; width: {{ $segment['size'] }}%; background: {{ $segment['color'] }}"></span>
                                        @endforeach
                                    </div>
                                @else
                                    @foreach ($bars as $bar)
                                        @php($value = $bar['raw'][$index])
                                        <div class="relative h-4">
                                            @if (is_numeric($value))
                                                <span @class(['absolute inset-y-0', $value < 0 ? 'rounded-l' : 'rounded-r']) style="{{ $value < 0 ? 'right: '.(100 - $zeroAt).'%' : 'left: '.$zeroAt.'%' }}; width: {{ $percent($value) }}%; background: {{ $bar['color'] }}"></span>
                                                @if ($values)
                                                    <span class="absolute top-1/2 -translate-y-1/2 px-1.5 font-medium whitespace-nowrap text-zinc-900 tabular-nums dark:text-zinc-100" style="{{ $value < 0 ? 'right: '.(100 - $zeroAt + $percent($value)).'%' : 'left: '.($zeroAt + $percent($value)).'%' }}">{{ $bar['values'][$index] }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex gap-2 pt-2">
                    {{-- The value axis: rounded ticks, right-aligned so the digits line up. --}}
                    <div class="relative shrink-0 text-right tabular-nums" style="height: {{ $height }}px" aria-hidden="true">
                        @foreach ($ticks as $value)
                            <span class="absolute right-0 -translate-y-1/2 whitespace-nowrap" style="top: {{ 100 - ($value - $min) / $span * 100 }}%">{{ $tick($value) }}</span>
                        @endforeach
                        <span class="invisible block whitespace-nowrap">{{ collect($ticks)->map($tick)->sortByDesc(fn ($text) => mb_strlen($text))->first() }}</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div
                            tabindex="0"
                            role="group"
                            aria-roledescription="chart"
                            aria-label="{{ $label }}"
                            x-on:keydown="key($event)"
                            x-on:blur="index = null"
                            x-on:pointerleave="if (document.activeElement !== $el) index = null"
                            class="relative rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text"
                            style="height: {{ $height }}px"
                        >
                            {{-- Hairline gridlines, solid; the zero line a step stronger. --}}
                            @foreach ($ticks as $value)
                                <div @class(['pointer-events-none absolute inset-x-0 h-px', $value == 0 ? 'bg-zinc-300 dark:bg-zinc-700' : 'bg-zinc-200 dark:bg-zinc-800']) style="top: {{ 100 - ($value - $min) / $span * 100 }}%" aria-hidden="true"></div>
                            @endforeach

                            {{-- One band per category: it's the hover target, so the pointer never has to land on a thin bar. --}}
                            <div class="absolute inset-0 flex" aria-hidden="true">
                                @foreach ($labels as $index => $text)
                                    <div class="relative flex h-full min-w-0 flex-1 justify-center" x-on:pointerenter="index = {{ $index }}">
                                        <div class="pointer-events-none absolute inset-y-0 inset-x-[6%] rounded-sm" x-bind:class="index === {{ $index }} && 'bg-zinc-100 dark:bg-zinc-800/70'"></div>
                                        <div class="relative flex h-full w-3/4 justify-center gap-0.5">
                                        @if ($stacked)
                                            <div class="relative h-full w-full max-w-6">
                                                @foreach ($stacks[$index]['segments'] as $segment)
                                                    <span @class(['absolute inset-x-0 border-y border-(--gui-chart-surface)', $segment['negative'] ? 'rounded-b' : 'rounded-t' => $segment['rounded']]) style="bottom: {{ $segment['start'] }}%; height: {{ $segment['size'] }}%; background: {{ $segment['color'] }}"></span>
                                                @endforeach
                                                @if ($values && $stacks[$index]['total'] != 0)
                                                    <span class="absolute left-1/2 -translate-x-1/2 pb-1 font-medium whitespace-nowrap text-zinc-900 tabular-nums dark:text-zinc-100" style="bottom: {{ $zeroAt + max(0, $stacks[$index]['total']) / $span * 100 }}%">{{ $format($stacks[$index]['total']) }}</span>
                                                @endif
                                            </div>
                                        @else
                                            @foreach ($bars as $bar)
                                                @php($value = $bar['raw'][$index])
                                                <div class="relative h-full w-full max-w-6 min-w-0">
                                                    @if (is_numeric($value))
                                                        <span @class(['absolute inset-x-0', $value < 0 ? 'rounded-b' : 'rounded-t']) style="{{ $value < 0 ? 'top: '.(100 - $zeroAt).'%' : 'bottom: '.$zeroAt.'%' }}; height: {{ $percent($value) }}%; background: {{ $bar['color'] }}"></span>
                                                        @if ($values)
                                                            <span class="absolute left-1/2 -translate-x-1/2 font-medium whitespace-nowrap text-zinc-900 tabular-nums dark:text-zinc-100" style="{{ $value < 0 ? 'top: calc('.(100 - $zeroAt + $percent($value)).'% + 0.25rem)' : 'bottom: calc('.($zeroAt + $percent($value)).'% + 0.25rem)' }}">{{ $bar['values'][$index] }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- The tooltip: the category, then every series there, values first. --}}
                            <template x-if="index !== null">
                                <div
                                    class="pointer-events-none absolute top-2 z-10 min-w-32 rounded-gui border border-zinc-200 bg-white px-3 py-2 text-xs shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
                                    x-bind:style="(index + 0.5) / chart.labels.length > 0.55 ? `right: calc(${100 - index / chart.labels.length * 100}% + 0.5rem)` : `left: calc(${(index + 1) / chart.labels.length * 100}% + 0.5rem)`"
                                    aria-hidden="true"
                                >
                                    <p class="mb-1 font-medium text-zinc-900 dark:text-zinc-100" x-text="chart.labels[index]"></p>
                                    <template x-for="bar in chart.series" x-bind:key="bar.name">
                                        <p class="flex items-center gap-2 whitespace-nowrap">
                                            <span class="size-2.5 shrink-0 rounded-[2px]" x-bind:style="`background: ${bar.color}`"></span>
                                            <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100" x-text="bar.values[index]"></span>
                                            <span x-show="bar.name" x-text="bar.name"></span>
                                        </p>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Category labels, centred under their bands; hidden ones are only skipped on narrow screens. --}}
                        <div class="mt-2 flex" aria-hidden="true">
                            @foreach ($labels as $index => $text)
                                <span @class(['min-w-0 flex-1 truncate text-center', 'invisible @md:visible' => $count > 6 && $index % 2 === 1])>{{ $text }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <p class="sr-only" aria-live="polite" x-text="readout"></p>
        </div>

        <x-gui::chart.table :visible="$table" :label="$label" :labels="$labels" :columns="collect($bars)->map(fn ($bar) => ['name' => $bar['name'], 'values' => $bar['exact']])->all()" />
    @endif
</figure>
