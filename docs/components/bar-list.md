# Bar list

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A ranked list with a bar behind each name: top pages, referrers, countries. A real list, with the bars as decoration.

```blade
<x-gui::chart.bar-list :data="$visitsByPage" />
```

## Top pages

Sorted largest first. Each bar is as long as its share of the largest. Rows can link with href.

```blade
<x-gui::chart.bar-list
    class="max-w-md"
    label="Top pages"
    :data="[
        '/pricing' => ['value' => 4820, 'href' => '#'],
        '/' => ['value' => 12410, 'href' => '#'],
        '/docs/installation' => ['value' => 3105, 'href' => '#'],
        '/blog/tailwind-v4' => ['value' => 2290, 'href' => '#'],
        '/changelog' => ['value' => 870, 'href' => '#'],
    ]"
    compact
/>
```

## With shares, in a colour

percent adds each row's share of the total. limit keeps the top few (the share still counts every row). color takes any chart or theme colour.

```blade
<x-gui::chart.bar-list
    class="max-w-md"
    label="Visitors by country"
    :data="['United Kingdom' => 3820, 'United States' => 5210, 'Germany' => 1460, 'Australia' => 980, 'Japan' => 760, 'Brazil' => 420]"
    :limit="5"
    percent
    color="chart-3"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `data` | `array` | `[]` | name =&gt; value, or name =&gt; ['value' =&gt; …, 'href' =&gt; …]. |
| `sort` | `bool` | `true` | Largest first. Turn off to keep your order. |
| `limit` | `int` | `null` | Show only the top few. |
| `percent` | `bool` | `false` | Each row's share of the total, as well as its value. |
| `color` | `string` | `'chart-1'` | A chart or theme colour for the bars, softened behind the names. |
| `prefix, suffix, decimals, compact` | `string, string, int, bool` | `'', '', auto, false` | How values read. |
| `label` | `string` | `null` | The list's accessible name. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
