# Number input

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native number input with − and + buttons.

```blade
<x-gui::number-input name="quantity" min="1" max="10" value="1" />
```

## In a field

Type a number, use the arrow keys, or press − and +. The buttons use the browser's own stepping, so step, min and max behave exactly as they do on the input, and they turn off at the limits. They are left out of the tab order because the arrow keys do the same thing.

```blade
<x-gui::field label="Guests" name="guests" hint="Up to 8 per booking.">
    <x-gui::number-input min="1" max="8" value="2" class="max-w-40" />
</x-gui::field>
```

## Decimal steps

```blade
<x-gui::field label="Weight (kg)" name="weight">
    <x-gui::number-input step="0.5" min="0" value="2.5" class="max-w-40" />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |
| `decrement-label` | `string` | `'Decrease'` | The − button's accessible name. |
| `increment-label` | `string` | `'Increase'` | The + button's accessible name. |

min, max, step, wire:model and x-model go on the input. Pressing − or + fires input and change, so bindings update.

Without JavaScript it is a plain number input with the browser's own spinners.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
