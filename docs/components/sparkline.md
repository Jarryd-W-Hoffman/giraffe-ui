# Sparkline

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A tiny trend line for a stat or a table row. The line is quiet and the latest point is the accent, so the eye lands on now.

```blade
<x-gui::sparkline :data="$visitsByDay" />
```

## In stats

Sized by its classes (h-8 w-24 by default). Screen readers hear the trend's start, end, high and low.

```blade
<div class="grid w-full gap-4 sm:grid-cols-2">
    <x-gui::card>
        <div class="flex items-end justify-between gap-4">
            <x-gui::stat label="Visitors" value="12,840" change="8%" trend="up" description="last 12 weeks" />
            <x-gui::sparkline :data="[820, 932, 901, 934, 1290, 1330, 1320, 1180, 1250, 1410, 1380, 1520]" class="h-10 w-28" />
        </div>
    </x-gui::card>
    <x-gui::card>
        <div class="flex items-end justify-between gap-4">
            <x-gui::stat label="Refunds" value="1.8%" change="0.3 pts" trend="down" invert description="last 12 weeks" />
            <x-gui::sparkline :data="[2.6, 2.4, 2.5, 2.2, 2.3, 2.1, 2.2, 1.9, 2.0, 1.9, 1.7, 1.8]" suffix="%" class="h-10 w-28" />
        </div>
    </x-gui::card>
</div>
```

## With an area, or in a colour

area adds a soft wash under the line. color draws the whole line in one of the chart colours (chart-1 to chart-8) or a theme colour such as success.

```blade
<div class="flex items-center gap-8">
    <x-gui::sparkline :data="[4, 6, 5, 9, 7, 11, 10, 14]" area />
    <x-gui::sparkline :data="[4, 6, 5, 9, 7, 11, 10, 14]" color="chart-1" area />
    <x-gui::sparkline :data="[14, 12, 13, 9, 10, 7, 8, 5]" color="chart-2" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `data` | `array` | `[]` | The values, oldest first. null leaves a gap. |
| `color` | `string` | `null` | A chart colour (chart-1 to chart-8) or theme colour for the whole line. By default the line is grey and the latest point is chart-1. |
| `area` | `bool` | `false` | A soft fill under the line. |
| `prefix, suffix, compact` | `string, string, bool` | `'', '', false` | How values read in the summary, e.g. prefix="$" or suffix="%". |
| `label` | `string` | `null` | What screen readers hear. Defaults to the trend's start, end, high and low (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
