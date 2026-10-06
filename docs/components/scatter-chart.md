# Scatter chart

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

How two measures relate: one dot per item, placed by both. Each series has its own colour and shape.

```blade
<x-gui::chart.scatter :data="$campaigns" x-label="Spend" y-label="Signups" />
```

## One series

Points are [x, y], or ['x' =&gt; …, 'y' =&gt; …, 'label' =&gt; …] to name them in the tooltip and table. x-label and y-label title the axes. Hover a dot, or focus the chart and use the arrow keys.

```blade
<x-gui::chart.scatter
    name="Campaigns"
    x-label="Spend"
    y-label="Signups"
    x-prefix="$"
    :data="[
        ['x' => 1200, 'y' => 84, 'label' => 'Newsletter'],
        ['x' => 3400, 'y' => 190, 'label' => 'Search ads'],
        ['x' => 2100, 'y' => 96, 'label' => 'Podcast'],
        ['x' => 5200, 'y' => 240, 'label' => 'Social'],
        ['x' => 800, 'y' => 71, 'label' => 'Referrals'],
        ['x' => 4300, 'y' => 158, 'label' => 'Video'],
        ['x' => 2800, 'y' => 142, 'label' => 'Partners'],
    ]"
    :height="220"
/>
```

## Comparing groups

With series, each group gets a colour and a shape (circle, square, diamond, triangle), so they're told apart without colour too.

```blade
<x-gui::chart.scatter
    x-label="Session length (min)"
    y-label="Pages viewed"
    :series="[
        'Desktop' => [[4, 6], [7, 9], [12, 14], [9, 11], [15, 18], [6, 7], [11, 12]],
        'Mobile' => [[2, 3], [3, 5], [5, 4], [6, 8], [4, 4], [8, 7], [3, 2]],
        'Tablet' => [[5, 6], [8, 8], [10, 9], [7, 9]],
    ]"
    :zero="true"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `series` | `array` | `null` | name =&gt; points, for several series. |
| `data, name` | `array, string` | `null` | One series of points instead. |
| `x-label, y-label` | `string` | `null` | The axes' titles, also used in the tooltip and table. |
| `x-prefix, x-suffix` | `string` | `''` | How x values read. |
| `prefix, suffix, compact` | `string, string, bool` | `'', '', false` | How y values read (compact applies to both). |
| `zero` | `bool` | `false` | Start both axes at zero. By default they fit the data. |
| `colors` | `array` | `[]` | name =&gt; chart-1…chart-8, to pin a series to its colour. |
| `height` | `int` | `260` | The plot's height in pixels. |
| `label` | `string` | `null` | The chart's accessible name. Defaults to its series and axes. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
