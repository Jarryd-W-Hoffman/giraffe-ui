# Bar chart

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Compare amounts across categories. Thin bars with rounded data ends and a square base on zero, real text for every label, a hover and keyboard tooltip, and the numbers as a table.

```blade
<x-gui::chart.bar :labels="$weekdays" :data="$orders" name="Orders" />
```

## One series

Values sit at the end of each bar when there's one series and room for them; values="false" hides them.

```blade
<x-gui::chart.bar
    name="Orders"
    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    :data="[182, 214, 198, 246, 271, 140, 96]"
    :height="200"
/>
```

## Grouped

Several series sit side by side, 2px apart, with a legend. Hover anywhere in a column, or focus the chart and use the arrow keys.

```blade
<x-gui::chart.bar
    :labels="['Q1', 'Q2', 'Q3', 'Q4']"
    :series="['2025' => [42000, 51000, 48000, 63000], '2026' => [47000, 58000, 61000, 72000]]"
    prefix="$"
    compact
/>
```

## Stacked

stacked shows how parts make up each total, with a 2px gap between segments and the total above each bar.

```blade
<x-gui::chart.bar
    stacked
    :values="true"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
    :series="['Free' => [320, 340, 390, 410, 450, 480], 'Pro' => [120, 140, 150, 180, 210, 240], 'Team' => [40, 45, 60, 70, 85, 100]]"
/>
```

## Horizontal

horizontal suits long category names and ranked lists. Sort the data first: the chart keeps your order.

```blade
<x-gui::chart.bar
    horizontal
    name="Visitors"
    :labels="['Search engines', 'Direct', 'Newsletter', 'Social media', 'Partner sites']"
    :data="[48200, 31900, 12400, 9800, 4100]"
    compact
/>
```

## Negative values

Bars grow down from a stronger zero line.

```blade
<x-gui::chart.bar
    name="Net cash flow"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
    :data="[12400, -3200, 8100, -6900, 15300, 9800]"
    prefix="$"
    compact
    :height="200"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `labels` | `array` | `[]` | The categories, in order. |
| `series` | `array` | `null` | name =&gt; values for several series, values lined up with labels. |
| `data, name` | `array, string` | `null` | One series instead of series. |
| `colors` | `array` | `[]` | name =&gt; chart-1…chart-8, to pin a series to its colour. |
| `stacked` | `bool` | `false` | Stack series into one bar per category. |
| `horizontal` | `bool` | `false` | Bars run left to right, with category names down the side. |
| `values` | `bool` | `auto` | Value labels at the bar ends. On for one series of up to 12 bars. |
| `height` | `int` | `240` | The plot's height in pixels (vertical bars). |
| `prefix, suffix, decimals, compact` | `string, string, int, bool` | `'', '', auto, false` | How values read. |
| `label` | `string` | `null` | The chart's accessible name. Defaults to its series and range (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
