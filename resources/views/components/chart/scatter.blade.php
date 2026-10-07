@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'series' => null,
    'data' => null,
    'name' => null,
    'colors' => [],
    'height' => 260,
    'xLabel' => null,
    'yLabel' => null,
    'xPrefix' => '',
    'xSuffix' => '',
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'zero' => false,
    'label' => null,
    'table' => false,
])

@php
    // Points are [x, y] or ['x' => …, 'y' => …, 'label' => …], per series (name => points) or for one (data + name).
    $height = max(120, (int) $height);
    $xFormat = fn ($value, $decimals = null) => Chart::format($value, $xPrefix, $xSuffix, $decimals, $compact);
    $yFormat = fn ($value, $decimals = null) => Chart::format($value, $prefix, $suffix, $decimals, $compact);
    // Shape as well as colour tells the series apart, for colour-blind readers and in print.
    $shapes = ['circle', 'square', 'diamond', 'triangle'];

    $slot = 0;
    $groups = collect($series ?? [($name ?? '') => $data ?? []])->map(function ($points, $seriesName) use (&$slot, $colors, $shapes) {
        $index = $slot++;

        return [
            'name' => (string) $seriesName,
            'color' => 'var(--color-gui-'.($colors[$seriesName] ?? 'chart-'.(($index % 8) + 1)).')',
            'shape' => $shapes[$index % 4],
            'points' => collect($points)
                ->map(fn ($point) => array_is_list((array) $point) ? ['x' => $point[0] ?? null, 'y' => $point[1] ?? null, 'label' => $point[2] ?? null] : ['x' => $point['x'] ?? null, 'y' => $point['y'] ?? null, 'label' => $point['label'] ?? null])
                ->filter(fn ($point) => is_numeric($point['x']) && is_numeric($point['y']))
                ->values()
                ->all(),
        ];
    })->values()->all();
    $all = collect($groups)->flatMap(fn ($group) => $group['points']);
    $hasData = $all->isNotEmpty();

    if ($hasData) {
        ['min' => $yMin, 'max' => $yMax, 'ticks' => $yTicks] = Chart::ticks($all->pluck('y')->all(), $zero ? 0.0 : null, Chart::tickCount($height));
        ['min' => $xMin, 'max' => $xMax, 'ticks' => $xTicks] = Chart::ticks($all->pluck('x')->all(), $zero ? 0.0 : null, 6);
        $xDecimals = Chart::decimals($xTicks);
        $yDecimals = Chart::decimals($yTicks);
        $left = fn ($x) => round(($x - $xMin) / ($xMax - $xMin) * 100, 3);
        $top = fn ($y) => round(100 - ($y - $yMin) / ($yMax - $yMin) * 100, 3);
        $several = count($groups) > 1;

        // Every point, left to right, for the keyboard and the tooltip.
        $points = collect($groups)->flatMap(fn ($group) => collect($group['points'])->map(fn ($point, $index) => [
            'series' => $group['name'],
            'color' => $group['color'],
            'shape' => $group['shape'],
            'label' => (string) ($point['label'] ?? ''),
            'x' => $xFormat($point['x']),
            'y' => $yFormat($point['y']),
            'raw' => $point['x'],
            'left' => $left($point['x']),
            'top' => $top($point['y']),
        ]))->sortBy('raw')->values()->all();

        $names = collect($groups)->pluck('name')->filter()->join(', ', ' & ');
        $label ??= ($names ? $names.': ' : '').__('giraffeui::ui.scatter_summary', ['y' => $yLabel ?? 'y', 'x' => $xLabel ?? 'x']);
    }

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $markers = [
        'circle' => 'size-2.5 rounded-full',
        'square' => 'size-2.5 rounded-[2px]',
        'diamond' => 'size-2.5 rotate-45 rounded-[1px]',
        'triangle' => 'size-3 [clip-path:polygon(50%_0,100%_100%,0_100%)]',
    ];
@endphp

{{--
    A scatter chart: one dot per item, placed by two measures, to show how they relate. Each series has its own colour
    and shape. Hover a dot, or focus the chart and use the arrow keys to step through the dots left to right.
--}}
<figure {{ $attributes->class('@container min-w-0 text-xs text-zinc-600 dark:text-zinc-400') }}>
    @if (! $hasData)
        <p class="flex items-center justify-center rounded-gui border border-dashed border-zinc-200 text-sm dark:border-zinc-800" style="height: {{ $height }}px">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        @if ($several)
            <ul class="mb-3 flex flex-wrap gap-x-4 gap-y-1">
                @foreach ($groups as $group)
                    <li class="flex items-center gap-1.5">
                        <span class="shrink-0 {{ $markers[$group['shape']] }}" style="background: {{ $group['color'] }}" aria-hidden="true"></span>
                        {{ $group['name'] }}
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($yLabel)
            <p class="mb-1 font-medium text-zinc-700 dark:text-zinc-300" aria-hidden="true">{{ $yLabel }}</p>
        @endif

        <div
            class="flex gap-2 pt-2"
            x-data="{
                index: null,
                points: [],
                init() {
                    this.points = JSON.parse(this.$refs.data.textContent);
                },
                // The dot nearest the pointer, within reach; otherwise none.
                point(event) {
                    const box = this.$refs.plot.getBoundingClientRect();
                    let best = null, distance = 24 * 24;
                    this.points.forEach((point, index) => {
                        const dx = box.left + point.left / 100 * box.width - event.clientX;
                        const dy = box.top + point.top / 100 * box.height - event.clientY;
                        if (dx * dx + dy * dy < distance) [best, distance] = [index, dx * dx + dy * dy];
                    });
                    this.index = best;
                },
                key(event) {
                    const last = this.points.length - 1;
                    const moves = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
                    if (event.key in moves) this.index = Math.max(0, Math.min(last, (this.index ?? (moves[event.key] > 0 ? -1 : last + 1)) + moves[event.key]));
                    else if (event.key === 'Home') this.index = 0;
                    else if (event.key === 'End') this.index = last;
                    else if (event.key === 'Escape') this.index = null;
                    else return;
                    event.preventDefault();
                },
                get current() {
                    return this.index === null ? null : this.points[this.index];
                },
                get readout() {
                    const point = this.current;
                    if (! point) return '';
                    return [point.label, @js($xLabel ?? 'x') + ' ' + point.x, @js($yLabel ?? 'y') + ' ' + point.y, @js($several) ? point.series : ''].filter(Boolean).join(', ');
                },
            }"
        >
            <script type="application/json" x-ref="data">{!! json_encode($points, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

            <div class="relative shrink-0 text-right tabular-nums" style="height: {{ $height }}px" aria-hidden="true">
                @foreach ($yTicks as $value)
                    <span class="absolute right-0 -translate-y-1/2 whitespace-nowrap" style="top: {{ $top($value) }}%">{{ $yFormat($value, $yDecimals) }}</span>
                @endforeach
                <span class="invisible block whitespace-nowrap">{{ collect($yTicks)->map(fn ($value) => $yFormat($value, $yDecimals))->sortByDesc(fn ($text) => mb_strlen($text))->first() }}</span>
            </div>

            {{-- Room at the right for the last dot and the last x label, which centre on the plot's edge. --}}
            <div class="min-w-0 flex-1 pr-6 pl-1.5">
                <div
                    x-ref="plot"
                    tabindex="0"
                    role="group"
                    aria-roledescription="chart"
                    aria-label="{{ $label }}"
                    x-on:pointermove="point($event)"
                    x-on:pointerleave="if (document.activeElement !== $el) index = null"
                    x-on:keydown="key($event)"
                    x-on:blur="index = null"
                    class="relative rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text"
                    style="height: {{ $height }}px"
                >
                    {{-- Hairline gridlines both ways. --}}
                    @foreach ($yTicks as $value)
                        <div class="pointer-events-none absolute inset-x-0 h-px bg-zinc-200 dark:bg-zinc-800" style="top: {{ $top($value) }}%" aria-hidden="true"></div>
                    @endforeach
                    @foreach ($xTicks as $value)
                        <div class="pointer-events-none absolute inset-y-0 w-px bg-zinc-200 dark:bg-zinc-800" style="left: {{ $left($value) }}%" aria-hidden="true"></div>
                    @endforeach

                    {{-- The dots, ringed in the surface colour so overlapping ones stay distinct. --}}
                    @foreach ($points as $index => $point)
                        <span
                            class="pointer-events-none absolute -translate-x-1/2 -translate-y-1/2 ring-2 ring-(--gui-chart-surface) transition-transform {{ $markers[$point['shape']] }}"
                            x-bind:class="index === {{ $index }} && 'scale-150'"
                            style="left: {{ $point['left'] }}%; top: {{ $point['top'] }}%; background: {{ $point['color'] }}"
                            aria-hidden="true"
                        ></span>
                    @endforeach

                    <template x-if="current">
                        <div
                            class="pointer-events-none absolute z-10 min-w-32 -translate-y-full rounded-gui border border-zinc-200 bg-white px-3 py-2 text-xs shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
                            x-bind:style="`top: calc(${current.top}% - 0.75rem); ` + (current.left > 60 ? `right: calc(${100 - current.left}% - 0.5rem)` : `left: calc(${current.left}% - 0.5rem)`)"
                            aria-hidden="true"
                        >
                            <p class="mb-1 font-medium text-zinc-900 dark:text-zinc-100" x-show="current.label" x-text="current.label"></p>
                            <p class="whitespace-nowrap"><span>{{ $xLabel ?? 'x' }}</span> <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100" x-text="current.x"></span></p>
                            <p class="whitespace-nowrap"><span>{{ $yLabel ?? 'y' }}</span> <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100" x-text="current.y"></span></p>
                            @if ($several)
                                <p class="mt-1 flex items-center gap-1.5 whitespace-nowrap"><span class="size-2 shrink-0 rounded-full" x-bind:style="`background: ${current.color}`"></span><span x-text="current.series"></span></p>
                            @endif
                        </div>
                    </template>
                </div>

                <div class="relative mt-2 h-4" aria-hidden="true">
                    @foreach ($xTicks as $tickIndex => $value)
                        <span @class(['absolute top-0 -translate-x-1/2 whitespace-nowrap tabular-nums', 'hidden @md:inline' => $tickIndex % 2 === 1]) style="left: {{ $left($value) }}%">{{ $xFormat($value, $xDecimals) }}</span>
                    @endforeach
                </div>
                @if ($xLabel)
                    <p class="mt-1 text-center font-medium text-zinc-700 dark:text-zinc-300" aria-hidden="true">{{ $xLabel }}</p>
                @endif
            </div>

            <p class="sr-only" aria-live="polite" x-text="readout"></p>
        </div>

        <x-gui::chart.table :visible="$table"
            :label="$label"
            :labels="collect($points)->map(fn ($point, $index) => $point['label'] !== '' ? $point['label'] : '#'.($index + 1))->all()"
            :columns="array_values(array_filter([
                ['name' => $xLabel ?? 'x', 'values' => collect($points)->pluck('x')->all()],
                ['name' => $yLabel ?? 'y', 'values' => collect($points)->pluck('y')->all()],
                $several ? ['name' => __('giraffeui::ui.series'), 'values' => collect($points)->pluck('series')->all()] : null,
            ]))"
        />
    @endif
</figure>
