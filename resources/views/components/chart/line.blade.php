@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'labels' => [],
    'series' => null,
    'data' => null,
    'name' => null,
    'colors' => [],
    'area' => false,
    'stacked' => false,
    'curve' => false,
    'markers' => false,
    'highlight' => null,
    'reference' => null,
    'referenceLabel' => null,
    'zero' => true,
    'height' => 240,
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
])

@php
    $labels = array_values(collect($labels)->map(fn ($label) => (string) $label)->all());
    // One line (data + name) or several (series: name => values). Values line up with labels; null leaves a gap.
    $series = collect($series ?? [($name ?? '') => $data ?? []])
        ->map(fn ($values) => array_values(collect($values)->all()))
        ->all();
    $count = count($labels);
    $format = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals, $compact);
    // The data table is the exact version, so it never abbreviates.
    $exact = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals);
    // Stacked: each series sits on the ones before it, so the top line is the total. A gap stacks as zero.
    $stacked = $stacked && count($series) > 1;
    $area = $area || $stacked;
    $tops = [];
    if ($stacked) {
        $running = array_fill(0, $count, 0);
        foreach ($series as $seriesName => $values) {
            $lower = $running;
            foreach (range(0, max(0, $count - 1)) as $index) {
                $running[$index] += is_numeric($values[$index] ?? null) ? $values[$index] : 0;
            }
            $tops[$seriesName] = ['lower' => $lower, 'upper' => $running];
        }
    }
    $all = $stacked ? collect($tops)->pluck('upper')->flatten()->all() : collect($series)->flatten()->all();
    // A reference line (a target, a limit) is drawn within the axis, so it's never off the top.
    $referenceValue = is_numeric($reference) ? (float) $reference : null;
    $hasData = $count > 0 && collect($all)->contains(fn ($value) => is_numeric($value));
    $height = max(80, (int) $height);

    if ($hasData) {
        ['min' => $min, 'max' => $max, 'ticks' => $ticks] = Chart::ticks([...$all, $referenceValue], $zero || $area ? 0.0 : null, Chart::tickCount($height));
        // Axis ticks share their decimals (compact values round on their own).
        $tickDecimals = $decimals ?? Chart::decimals($ticks);
        $tick = fn ($value) => Chart::format($value, $prefix, $suffix, $tickDecimals, $compact);

        // Colour follows the series, not its position on screen: give a series its own slot with colors.
        $slot = 0;
        $lines = collect($series)->map(function ($values, $seriesName) use (&$slot, $colors, $min, $max, $count, $format, $exact, $stacked, $tops, $curve, $highlight) {
            $color = 'var(--color-gui-'.($colors[$seriesName] ?? 'chart-'.(($slot++ % 8) + 1)).')';
            // highlight: one series keeps its colour and the rest step back to grey, drawn underneath it.
            $muted = $highlight !== null && (string) $highlight !== (string) $seriesName;
            $color = $muted ? 'var(--gui-chart-muted)' : $color;
            $values = array_pad($values, $count, null);
            $last = array_key_last(array_filter($values, fn ($value) => is_numeric($value)));
            // Where the line is drawn: its own values, or the top of its band when stacked.
            $drawn = $stacked ? $tops[$seriesName]['upper'] : $values;

            return [
                'name' => (string) $seriesName,
                'color' => $color,
                'muted' => $muted,
                'line' => Chart::line($drawn, $min, $max, $curve),
                'area' => $stacked ? Chart::band($tops[$seriesName]['upper'], $tops[$seriesName]['lower'], $min, $max, $curve) : Chart::area($values, $min, $max, $curve),
                'ys' => array_map(fn ($value, $index) => is_numeric($value) ? Chart::y($drawn[$index], $min, $max) / 10 : null, $values, array_keys($values)),
                'values' => array_map(fn ($value) => $format($value), array_pad($values, $count, null)),
                'exact' => array_map(fn ($value) => $exact($value), array_pad($values, $count, null)),
                'last' => $last,
                'lastY' => $last === null ? null : Chart::y($drawn[$last], $min, $max) / 10,
                'lastValue' => $last === null ? null : $format($values[$last]),
            ];
        })->values()->all();
        // Grey lines first, so the highlighted one is drawn on top of them.
        $drawOrder = collect($lines)->sortBy(fn ($line) => $line['muted'] ? 0 : 1)->all();

        $xs = array_map(fn ($index) => Chart::x($index, $count) / 10, range(0, $count - 1));
        $several = count($lines) > 1;

        // Labels at the ends of the lines, when the chart is wide enough, unless any two would be closer than a line of text.
        $ends = collect($lines)->filter(fn ($line) => $line['lastY'] !== null)->sortBy('lastY')->values();
        $endLabels = $ends->count() <= 4 && $ends->sliding(2)->every(fn ($pair) => ($pair->last()['lastY'] - $pair->first()['lastY']) / 100 * $height >= 16);

        // Fewer x labels in a narrow chart: about four, or eight once the chart is 32rem wide.
        $stepSmall = max(1, (int) ceil($count / 4));
        $stepLarge = max(1, (int) ceil($count / 8));

        $label ??= __('giraffeui::ui.chart_summary', [
            'series' => collect($lines)->pluck('name')->filter()->join(', ', ' & ') ?: '',
            'from' => $labels[0],
            'to' => $labels[$count - 1],
        ]);
    }
@endphp

{{--
    A line chart drawn on the server: SVG for the lines, HTML for every piece of text, so labels stay their real size at
    any width. Hover or focus it and use the arrow keys for a crosshair that reads out every series at that point;
    "Show the data" opens the same numbers as a table. Colours come from --color-gui-chart-1 to 8, in order.
--}}
<figure {{ $attributes->class('@container min-w-0 text-xs text-zinc-600 dark:text-zinc-400') }}>
    @if (! $hasData)
        <p class="flex items-center justify-center rounded-gui border border-dashed border-zinc-200 text-sm dark:border-zinc-800" style="height: {{ $height }}px">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        @if ($several)
            <x-gui::chart.legend :items="$lines" class="mb-3" />
        @endif

        {{-- Room above the plot for the top tick's label, which is centred on the top line. --}}
        <div
            dir="ltr"
            class="flex gap-2 pt-2"
            x-data="{
                index: null,
                chart: { labels: [], xs: [], series: [] },
                init() {
                    this.chart = JSON.parse(this.$refs.data.textContent);
                },
                point(event) {
                    const box = this.$refs.plot.getBoundingClientRect();
                    const last = this.chart.labels.length - 1;
                    this.index = Math.max(0, Math.min(last, Math.round((event.clientX - box.left) / box.width * last)));
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
                // What a screen reader hears as the crosshair moves: the point, then every series' value there.
                get readout() {
                    if (this.index === null) return '';
                    return this.chart.labels[this.index] + ': ' + this.chart.series.map((line) => (line.name ? line.name + ' ' : '') + line.values[this.index]).join(', ');
                },
            }"
        >
            {{-- Labels and values are data: Alpine reads them with JSON.parse and shows them with x-text, never as HTML. --}}
            <script type="application/json" x-ref="data">{!! json_encode(['labels' => $labels, 'xs' => $xs, 'series' => collect($lines)->map(fn ($line) => ['name' => $line['name'], 'color' => $line['color'], 'ys' => $line['ys'], 'values' => $line['values']])->all()], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

            {{-- The y axis: rounded ticks, right-aligned so the digits line up. --}}
            <div class="relative shrink-0 text-right tabular-nums" style="height: {{ $height }}px" aria-hidden="true">
                @foreach ($ticks as $value)
                    <span class="absolute right-0 -translate-y-1/2 whitespace-nowrap" style="top: {{ Chart::y($value, $min, $max) / 10 }}%">{{ $tick($value) }}</span>
                @endforeach
                {{-- Reserve the widest tick's width. --}}
                <span class="invisible block whitespace-nowrap">{{ collect($ticks)->map($tick)->sortByDesc(fn ($text) => mb_strlen($text))->first() }}</span>
            </div>

            {{-- pr-2: room for the last points' markers, which sit on the plot's right edge. --}}
            <div class="min-w-0 flex-1 pr-2">
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
                    class="relative cursor-crosshair rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text"
                    style="height: {{ $height }}px"
                >
                    <svg viewBox="0 0 {{ Chart::SIZE }} {{ Chart::SIZE }}" preserveAspectRatio="none" aria-hidden="true" class="absolute inset-0 size-full overflow-visible">
                        {{-- Hairline gridlines, one shade off the surface, solid. --}}
                        <g class="text-zinc-200 dark:text-zinc-800" stroke="currentColor" stroke-width="1" vector-effect="non-scaling-stroke">
                            @foreach ($ticks as $value)
                                <line x1="0" x2="{{ Chart::SIZE }}" y1="{{ Chart::y($value, $min, $max) }}" y2="{{ Chart::y($value, $min, $max) }}" vector-effect="non-scaling-stroke" />
                            @endforeach
                        </g>
                        @if ($referenceValue !== null)
                            {{-- The reference line: dashed and neutral, so it never reads as another series. --}}
                            <line x1="0" x2="{{ Chart::SIZE }}" y1="{{ Chart::y($referenceValue, $min, $max) }}" y2="{{ Chart::y($referenceValue, $min, $max) }}" class="text-zinc-500 dark:text-zinc-400" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" vector-effect="non-scaling-stroke" />
                        @endif
                        @foreach ($drawOrder as $line)
                            <g style="color: {{ $line['color'] }}">
                                @if ($area && ! $line['muted'])
                                    <path d="{{ $line['area'] }}" fill="currentColor" fill-opacity="{{ $stacked ? 0.25 : 0.1 }}" />
                                @endif
                                <path d="{{ $line['line'] }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                            </g>
                        @endforeach
                    </svg>

                    @if ($markers)
                        {{-- A dot at every point, ringed in the surface colour: shape as well as colour, and gaps show. --}}
                        @foreach ($drawOrder as $line)
                            @foreach ($line['ys'] as $index => $y)
                                @if ($y !== null && $index !== $line['last'])
                                    <span class="pointer-events-none absolute size-2 -translate-x-1/2 -translate-y-1/2 rounded-full ring-2 ring-(--gui-chart-surface)" style="left: {{ $xs[$index] }}%; top: {{ $y }}%; background: {{ $line['color'] }}" aria-hidden="true"></span>
                                @endif
                            @endforeach
                        @endforeach
                    @endif

                    @if ($referenceValue !== null && $referenceLabel)
                        <span @class(['pointer-events-none absolute left-1 font-medium text-zinc-700 dark:text-zinc-300', Chart::y($referenceValue, $min, $max) < 100 ? 'pt-0.5' : '-translate-y-full pb-0.5']) style="top: {{ Chart::y($referenceValue, $min, $max) / 10 }}%" aria-hidden="true">{{ $referenceLabel }} · {{ $format($referenceValue) }}</span>
                    @endif

                    {{-- The latest point of each line, ringed in the surface colour so it reads where lines cross. --}}
                    @foreach ($drawOrder as $line)
                        @if ($line['lastY'] !== null)
                            <span class="pointer-events-none absolute size-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full ring-2 ring-(--gui-chart-surface)" style="left: {{ $xs[$line['last']] }}%; top: {{ $line['lastY'] }}%; background: {{ $line['color'] }}" aria-hidden="true"></span>
                        @endif
                    @endforeach

                    {{-- The crosshair snaps to the nearest point; its tooltip lists every line there, values first. --}}
                    <template x-if="index !== null">
                        <div aria-hidden="true">
                            <div class="pointer-events-none absolute inset-y-0 w-px -translate-x-1/2 bg-zinc-400 dark:bg-zinc-500" x-bind:style="`left: ${chart.xs[index]}%`"></div>
                            <template x-for="line in chart.series" x-bind:key="line.name">
                                <span x-show="line.ys[index] !== null" class="pointer-events-none absolute size-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full ring-2 ring-(--gui-chart-surface)" x-bind:style="`left: ${chart.xs[index]}%; top: ${line.ys[index]}%; background: ${line.color}`"></span>
                            </template>
                            <div
                                class="pointer-events-none absolute top-2 z-10 min-w-32 rounded-gui border border-zinc-200 bg-white px-3 py-2 text-xs shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
                                x-bind:style="chart.xs[index] > 55 ? `right: calc(${100 - chart.xs[index]}% + 0.75rem)` : `left: calc(${chart.xs[index]}% + 0.75rem)`"
                            >
                                <p class="mb-1 font-medium text-zinc-900 dark:text-zinc-100" x-text="chart.labels[index]"></p>
                                <template x-for="line in chart.series" x-bind:key="line.name">
                                    <p class="flex items-center gap-2 whitespace-nowrap">
                                        <span class="h-0.5 w-3 shrink-0 rounded-full" x-bind:style="`background: ${line.color}`"></span>
                                        <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100" x-text="line.values[index]"></span>
                                        <span x-show="line.name" x-text="line.name"></span>
                                    </p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- The x axis: first and last labels sit inside the plot's edges; fewer labels on narrow screens. --}}
                <div class="relative mt-2 h-4" aria-hidden="true">
                    @foreach ($labels as $index => $text)
                        <span @class([
                            'absolute top-0 whitespace-nowrap',
                            '-translate-x-1/2' => $index > 0 && $index < $count - 1,
                            '-translate-x-full' => $index === $count - 1 && $count > 1,
                            'hidden' => $index % $stepSmall !== 0,
                            '@lg:inline' => $index % $stepSmall !== 0 && $index % $stepLarge === 0,
                            '@lg:hidden' => $index % $stepSmall === 0 && $index % $stepLarge !== 0,
                        ]) style="left: {{ $xs[$index] }}%">{{ $text }}</span>
                    @endforeach
                </div>
            </div>

            @if ($endLabels)
                {{-- Direct labels at the ends of the lines: the name (or, for one line, its latest value). --}}
                <div class="relative hidden w-20 shrink-0 @lg:block" style="height: {{ $height }}px" aria-hidden="true">
                    @foreach ($lines as $line)
                        @if ($line['lastY'] !== null)
                            <span class="absolute left-0 max-w-full -translate-y-1/2 truncate font-medium text-zinc-900 dark:text-zinc-100" style="top: {{ $line['lastY'] }}%">{{ $several ? $line['name'] : $line['lastValue'] }}</span>
                        @endif
                    @endforeach
                </div>
            @endif

            <p class="sr-only" aria-live="polite" x-text="readout"></p>
        </div>

        <x-gui::chart.table :label="$label" :labels="$labels" :columns="collect($lines)->map(fn ($line) => ['name' => $line['name'], 'values' => $line['exact']])->all()" />
    @endif
</figure>
