# Donut chart

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A part-to-whole with a few parts. The list beside it is the legend and the numbers in one, so nobody has to judge an arc by eye.

```blade
<x-gui::chart.donut :data="['Card' => 6420, 'PayPal' => 2310, 'Bank transfer' => 980]" prefix="$" />
```

## Basic

Pass name =&gt; value, largest first: the chart keeps your order. Hover a part or a row, or focus it and use the arrow keys, to show that part in the centre.

```blade
<x-gui::chart.donut
    :data="['Card' => 6420, 'PayPal' => 2310, 'Bank transfer' => 980, 'Gift card' => 290]"
    prefix="$"
    compact
/>
```

## Smaller, with a custom centre

size and thickness are in pixels and viewBox units. center replaces the total. Past eight parts, the rest fold into "Other".

```blade
<x-gui::chart.donut
    :size="120"
    :thickness="12"
    center="68 GB"
    :data="['Photos' => 31, 'Video' => 18, 'Documents' => 9, 'Apps' => 6, 'Other' => 4]"
    suffix=" GB"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `data` | `array` | `[]` | name =&gt; value. Zero and negative values are left out. |
| `colors` | `array` | `[]` | name =&gt; chart-1…chart-8, to pin a part to its colour. |
| `size` | `int` | `160` | The donut's width and height in pixels. |
| `thickness` | `int` | `14` | The ring's thickness, out of 100. |
| `center` | `string` | `null` | What the centre shows instead of the total. |
| `prefix, suffix, decimals, compact` | `string, string, int, bool` | `'', '', auto, false` | How values read. |
| `label` | `string` | `null` | The chart's accessible name. Defaults to every part and its share. |

Keep it to a few parts. To compare many, or compare across groups, use a bar chart: lengths are easier to judge than angles.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
