# Swatches

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Pick a colour from a set of swatches. Native radios, so one tab stop, arrow keys and forms all work.

```blade
<x-gui::swatches name="color" label="Colour" :options="['violet' => '#7c3aed', 'emerald' => '#059669']" />
```

## Basic

options are value =&gt; colour, or value =&gt; ['label' =&gt; …, 'color' =&gt; …]; each swatch is named for screen readers. x-model and wire:model bind every radio.

```blade
<x-gui::swatches
    name="accent"
    label="Accent colour"
    value="violet"
    :options="[
        'indigo' => 'var(--color-indigo-600)',
        'violet' => 'var(--color-violet-600)',
        'sky' => 'var(--color-sky-600)',
        'emerald' => 'var(--color-emerald-600)',
        'rose' => ['label' => 'Rose', 'color' => 'var(--color-rose-600)'],
        'orange' => 'var(--color-orange-600)',
    ]"
/>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `name` | `string` | `` | The radios' name. |
| `options` | `array` | `[]` | value =&gt; CSS colour, or value =&gt; ['label' =&gt; …, 'color' =&gt; …]. |
| `value` | `string` | `null` | The chosen value. |
| `label` | `string` | `null` | The group's legend. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
