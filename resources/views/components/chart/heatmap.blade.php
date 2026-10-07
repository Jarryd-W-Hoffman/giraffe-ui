@use('JayAitch\GiraffeUI\Support\Chart')

@props([
    'rows' => [],
    'columns' => [],
    'data' => [],
    'color' => 'chart-1',
    'prefix' => '',
    'suffix' => '',
    'compact' => false,
    'decimals' => null,
    'label' => null,
])

@php
    // data is one list of values per row, lined up with columns; null is "no data".
    $rows = array_values(array_map('strval', collect($rows)->all()));
    $columns = array_values(array_map('strval', collect($columns)->all()));
    $values = collect($rows)->keys()->map(fn ($r) => array_pad(array_values(collect(collect($data)->values()->get($r, []))->all()), count($columns), null))->all();
    $numbers = collect($values)->flatten()->filter(fn ($value) => is_numeric($value));
    $hasData = $rows && $columns && $numbers->isNotEmpty();
    $max = max(1e-9, (float) $numbers->max());
    $format = fn ($value) => Chart::format($value, $prefix, $suffix, $decimals, $compact);

    // One hue, light to dark: five steps of the colour mixed into the chart surface, so more always reads as stronger
    // in light and dark mode. Zero and missing values are the empty cell colour.
    $steps = [18, 36, 56, 78, 100];
    $fill = fn ($step) => "color-mix(in oklab, var(--color-gui-{$color}) {$step}%, var(--gui-chart-surface))";
    $step = fn ($value) => is_numeric($value) && $value > 0 ? $steps[min(4, max(0, (int) ceil($value / $max * 5) - 1))] : null;

    // Fewer column labels when there are many: about every 6th on narrow charts, every 3rd from 32rem.
    $count = count($columns);
    // A week's worth of short labels fits; more than that thins out.
    $everySmall = $count <= 8 ? 1 : max(1, (int) ceil($count / 6));
    $everyLarge = max(1, (int) ceil($count / 12));
    $label ??= __('giraffeui::ui.heatmap_summary', ['rows' => count($rows), 'columns' => $count]);
@endphp

{{--
    A heatmap: a table of values, each cell shaded by its value on a single-hue scale. It's a real table, so screen
    readers move through it and read every value; hover a cell, or focus it and use the arrow keys, for a tooltip.
--}}
<figure {{ $attributes->class('@container min-w-0 text-xs text-zinc-600 dark:text-zinc-400') }}>
    @if (! $hasData)
        <p class="flex h-40 items-center justify-center rounded-gui border border-dashed border-zinc-200 text-sm dark:border-zinc-800">{{ __('giraffeui::ui.no_data') }}</p>
    @else
        <div
            class="relative"
            x-data="{
                row: null,
                column: null,
                rows: @js($rows),
                columns: @js($columns),
                values: @js(collect($values)->map(fn ($row) => array_map($format, $row))->all()),
                key(event) {
                    const moves = { ArrowRight: [0, 1], ArrowLeft: [0, -1], ArrowDown: [1, 0], ArrowUp: [-1, 0] };
                    if (event.key in moves) {
                        const [dr, dc] = moves[event.key];
                        this.row = Math.max(0, Math.min(this.rows.length - 1, (this.row ?? 0) + (this.row === null ? 0 : dr)));
                        this.column = Math.max(0, Math.min(this.columns.length - 1, (this.column ?? 0) + (this.column === null ? 0 : dc)));
                    } else if (event.key === 'Home') this.column = 0;
                    else if (event.key === 'End') this.column = this.columns.length - 1;
                    else if (event.key === 'Escape') this.row = this.column = null;
                    else return;
                    this.row ??= 0;
                    this.column ??= 0;
                    event.preventDefault();
                },
                active(r, c) {
                    return this.row === r && this.column === c;
                },
                get readout() {
                    if (this.row === null) return '';
                    return this.rows[this.row] + ', ' + this.columns[this.column] + ': ' + this.values[this.row][this.column];
                },
            }"
            x-on:pointerleave="if (! $el.contains(document.activeElement)) row = column = null"
        >
            <div
                tabindex="0"
                role="group"
                aria-roledescription="chart"
                aria-label="{{ $label }}"
                x-on:keydown="key($event)"
                x-on:blur="row = column = null"
                class="overflow-x-auto rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text"
            >
                <table class="w-full table-fixed border-separate border-spacing-[3px]">
                    <caption class="sr-only">{{ $label }}</caption>
                    <colgroup>
                        <col class="w-12 @md:w-16">
                    </colgroup>
                    <thead>
                        <tr>
                            <td></td>
                            @foreach ($columns as $c => $text)
                                <th scope="col" class="truncate pb-1 text-center font-normal tabular-nums">
                                    <span @class(['hidden' => $c % $everySmall !== 0, '@md:inline' => $c % $everyLarge === 0 && $c % $everySmall !== 0, '@md:hidden' => $c % $everyLarge !== 0 && $c % $everySmall === 0]) aria-hidden="true">{{ $text }}</span>
                                    <span class="sr-only">{{ $text }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r => $rowLabel)
                            <tr>
                                <th scope="row" class="truncate pe-1 text-end font-normal">{{ $rowLabel }}</th>
                                @foreach ($values[$r] as $c => $value)
                                    @php($shade = $step($value))
                                    <td class="p-0" x-on:pointerenter="row = {{ $r }}; column = {{ $c }}">
                                        <div
                                            @class(['aspect-square min-h-3 rounded-[3px] ring-offset-1 ring-offset-(--gui-chart-surface)', 'bg-zinc-100 dark:bg-zinc-800' => $shade === null])
                                            @if ($shade !== null) style="background: {{ $fill($shade) }}" @endif
                                            x-bind:class="active({{ $r }}, {{ $c }}) && 'ring-2 ring-zinc-900 dark:ring-zinc-100'"
                                        ></div>
                                        <span class="sr-only">{{ $format($value) }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <template x-if="row !== null">
                <div class="pointer-events-none absolute top-0 end-0 z-10 rounded-gui border border-zinc-200 bg-white px-3 py-2 shadow-lg dark:border-zinc-700 dark:bg-zinc-900" aria-hidden="true">
                    <p class="text-zinc-600 dark:text-zinc-400"><span x-text="rows[row]"></span> · <span x-text="columns[column]"></span></p>
                    <p class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100" x-text="values[row][column]"></p>
                </div>
            </template>

            <p class="sr-only" aria-live="polite" x-text="readout"></p>
        </div>

        {{-- The scale, from empty to the most. --}}
        <div class="mt-2 flex items-center justify-end gap-1.5" aria-hidden="true">
            <span>{{ $format(0) }}</span>
            <span class="size-3 rounded-[3px] bg-zinc-100 dark:bg-zinc-800"></span>
            @foreach ($steps as $shade)
                <span class="size-3 rounded-[3px]" style="background: {{ $fill($shade) }}"></span>
            @endforeach
            <span>{{ $format($max) }}</span>
        </div>
    @endif
</figure>
