# Slider

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native range input in the theme colour, with an optional value and tick marks.

```blade
<x-gui::slider name="volume" min="0" max="100" value="60" show-value suffix="%" />
```

## With its value

prefix and suffix format the shown value and aria-valuetext, so screen readers hear "8 hours" rather than "8".

```blade
<x-gui::field label="Daily focus time" name="focus">
    <x-gui::slider min="0" max="12" value="8" show-value suffix=" hours" />
</x-gui::field>
```

## Tick marks

marks become a datalist, which browsers can draw as ticks and snap towards. Give labels as value =&gt; label pairs.

```blade
<x-gui::field label="Budget" name="budget">
    <x-gui::slider min="0" max="1000" step="50" value="400" :marks="[0, 250, 500, 750, 1000]" show-value prefix="$" />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `show-value` | `bool` | `false` | Show the current value beside the slider, in an output element. |
| `prefix` | `string` | `''` | Before the value, e.g. $. Also used in aria-valuetext. |
| `suffix` | `string` | `''` | After the value, e.g. %. Also used in aria-valuetext. |
| `marks` | `array` | `[]` | Values for tick marks, or value =&gt; label pairs. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

min, max, step, wire:model and x-model go on the input. Without JavaScript the slider still works; the shown value just doesn't update.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
