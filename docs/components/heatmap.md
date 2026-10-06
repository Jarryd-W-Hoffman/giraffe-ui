# Heatmap

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A grid of values, each cell shaded on a single-hue scale: busy hours, activity by day. A real table, so every value can be read.

```blade
<x-gui::chart.heatmap :rows="$days" :columns="$hours" :data="$ordersByDayAndHour" />
```

## Orders by day and hour

data has one list per row, lined up with columns. More is darker in light mode and brighter in dark mode. Hover a cell, or focus the chart and use the arrow keys; screen readers can also move through it as a table.

```blade
@php
    $hours = array_map(fn ($hour) => sprintf('%02d', $hour), range(8, 20));
    $shape = [1, 2, 4, 6, 7, 9, 10, 8, 6, 5, 7, 8, 4];
    $orders = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
        ->map(fn ($day, $d) => array_map(fn ($base, $h) => (int) round($base * ($d < 5 ? 3 : 1.6) + (($d * 7 + $h * 3) % 5)), $shape, array_keys($shape)))
        ->all();
@endphp
<x-gui::chart.heatmap
    label="Orders by day and hour"
    :rows="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    :columns="$hours"
    :data="$orders"
/>
```

## In another colour

color takes one chart or theme colour. null cells show as empty.

```blade
<x-gui::chart.heatmap
    class="max-w-sm"
    label="Deploys by week"
    color="chart-3"
    :rows="['Wk 1', 'Wk 2', 'Wk 3', 'Wk 4']"
    :columns="['M', 'T', 'W', 'T', 'F']"
    :data="[[3, 5, 2, 6, 1], [4, null, 7, 5, 2], [1, 3, 4, 8, 3], [5, 6, 2, 4, 0]]"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `rows, columns` | `array` | `[]` | The labels down the side and across the top. |
| `data` | `array` | `[]` | One list of values per row, lined up with columns. null is no data. |
| `color` | `string` | `'chart-1'` | The hue, from empty to full strength, in five steps. |
| `prefix, suffix, decimals, compact` | `string, string, int, bool` | `'', '', auto, false` | How values read. |
| `label` | `string` | `null` | The chart's accessible name, also its table caption. |

Shades come from mixing the colour into --gui-chart-surface, so they suit light and dark mode. Values above zero are split into five equal steps of the largest.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
