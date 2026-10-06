# Line chart

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Change over time, for one series or a few. Drawn on the server as SVG, with real text for every label, a keyboard-friendly crosshair and the numbers as a table.

```blade
<x-gui::chart.line :labels="$months" :series="['Revenue' => $revenue, 'Costs' => $costs]" prefix="$" compact />
```

## One series

A single line needs no legend: say what it is in the heading around it. Its latest value is labelled at the end. Hover, or focus it and use the arrow keys, for the value at each point.

```blade
<x-gui::chart.line
    name="Revenue"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
    :data="[18200, 21400, 19800, 24600, 27100, 26300, 29800, 31200, 30400, 34900, 38100, 42600]"
    prefix="$"
    compact
    area
/>
```

## Comparing series

Two or more lines get a legend, and labels at their ends when there's room. The tooltip lists every line at that point, so you never have to land on one. Each series keeps its colour slot, in order; pass colors to pin one, so filtering others out never repaints it.

```blade
<x-gui::chart.line
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
    :series="[
        'Online' => [12400, 14100, 13200, 16900, 18800, 18100, 20900, 22400, 21300, 24800, 27600, 31200],
        'In store' => [5800, 7300, 6600, 7700, 8300, 8200, 8900, 8800, 9100, 10100, 10500, 11400],
    ]"
    prefix="$"
    compact
/>
```

## Highlight one series

highlight keeps one line in colour and steps the rest back to grey, for "how are we doing against everyone else".

```blade
<x-gui::chart.line
    highlight="Acme"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']"
    :series="[
        'Acme' => [12, 14, 13, 17, 19, 22, 24, 27],
        'Globex' => [18, 17, 19, 18, 20, 19, 21, 20],
        'Initech' => [9, 11, 10, 12, 11, 13, 12, 14],
        'Umbrella' => [15, 16, 14, 15, 13, 14, 12, 13],
    ]"
    suffix="%"
    :height="200"
/>
```

## Curves, markers and a target

curve smooths the line without overshooting, so every peak is a real value. markers puts a dot on each point. reference draws a dashed line, such as a target, with reference-label.

```blade
<x-gui::chart.line
    name="Signups"
    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    :data="[42, 58, 51, 74, 69, 38, 33]"
    curve
    markers
    :reference="60"
    reference-label="Target"
    :height="200"
/>
```

## Gaps and an honest baseline

A missing value (null) breaks the line rather than pretending it was zero. The y axis starts at zero by default so changes aren't exaggerated; zero="false" fits it to the data instead, for values that never get near zero.

```blade
<x-gui::chart.line
    name="Uptime"
    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    :data="[99.95, 99.97, null, 99.91, 99.98, 99.99, 99.96]"
    suffix="%"
    :zero="false"
    :height="180"
/>
```

## No data yet

```blade
<x-gui::chart.line name="Signups" :labels="[]" :data="[]" :height="160" />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `labels` | `array` | `[]` | The x axis, in order: dates, months, weeks. |
| `series` | `array` | `null` | name =&gt; values for several lines, values lined up with labels. Up to 8. |
| `data, name` | `array, string` | `null` | One line instead of series. |
| `colors` | `array` | `[]` | name =&gt; chart-1…chart-8, to pin a series to its colour. |
| `area` | `bool` | `false` | A soft fill under each line. Best for one series. |
| `stacked` | `bool` | `false` | Stack the series, so the top line is the total. Turns area on. |
| `curve` | `bool` | `false` | Smooth curves that never overshoot the data. |
| `markers` | `bool` | `false` | A dot at every point. |
| `highlight` | `string` | `null` | The one series to keep in colour; the others turn grey. |
| `reference, reference-label` | `number, string` | `null` | A dashed reference line, such as a target or limit, and its label. |
| `zero` | `bool` | `true` | Start the y axis at zero. Turn off for values that never get near it. |
| `height` | `int` | `240` | The plot's height in pixels (axis labels are extra). |
| `prefix, suffix, decimals, compact` | `string, string, int, bool` | `'', '', auto, false` | How values read on the axis, in the tooltip and table. compact gives 12.9K, 4.2M. |
| `label` | `string` | `null` | The chart's accessible name. Defaults to its series and range (translatable). |

Colours come from --color-gui-chart-1 to 8 in the theme, in a fixed order validated for colour-blind readers. Override them in your @theme, and re-check the set.

Markers are ringed in --gui-chart-surface (white, or zinc-900 in dark mode, to match cards). Set it on a wrapper if the chart sits on another colour.

Text never takes a series colour: labels, values and the legend use the normal text colours, with a coloured key beside them.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
