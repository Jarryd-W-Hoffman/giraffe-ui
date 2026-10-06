# Area chart

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

How parts make up a total over time. A line chart with its fill on: several series stack, so the top edge is the total and each band is that series' share.

```blade
<x-gui::chart.area :labels="$months" :series="['Free' => $free, 'Pro' => $pro]" />
```

## Stacked

The tooltip shows each series' own value, not the running total. Takes every line chart option: curve, markers, highlight, reference.

```blade
<x-gui::chart.area
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
    :series="[
        'Free' => [3200, 3400, 3900, 4100, 4500, 4800, 5100, 5300, 5800, 6100, 6400, 6900],
        'Pro' => [1200, 1400, 1500, 1800, 2100, 2400, 2500, 2800, 3000, 3300, 3700, 4000],
        'Team' => [400, 450, 600, 700, 850, 1000, 1100, 1300, 1400, 1600, 1800, 2100],
    ]"
    compact
    curve
/>
```

## One series

```blade
<x-gui::chart.area
    name="Storage"
    :labels="['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6']"
    :data="[1.2, 1.5, 1.9, 2.4, 2.6, 3.1]"
    suffix=" TB"
    :reference="4"
    reference-label="Plan limit"
    :height="180"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `stacked` | `bool` | `true` | Stack several series. stacked="false" overlaps them, best kept to two. |
| `…` | `` | `` | Every Line chart prop: labels, series or data and name, colors, curve, markers, highlight, reference, height and formatting. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
