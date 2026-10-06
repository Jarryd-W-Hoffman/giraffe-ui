# Time picker

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native time input with a list of times to pick from, written the way the visitor's locale writes them.

```blade
<x-gui::time-picker name="starts_at" />
```

## Basic

Type a time, or open the list: arrow keys move, Enter picks, Escape closes. It submits H:i, such as 14:30.

```blade
<x-gui::field label="Start time" name="start_time">
    <x-gui::time-picker value="09:30" />
</x-gui::field>
```

## Opening hours, every 15 minutes

interval sets the list's spacing in minutes. min and max limit the list and the browser's own validation; step makes typed times keep to the interval too (in seconds).

```blade
<x-gui::field label="Pickup time" name="pickup" hint="Between 8 AM and 6 PM.">
    <x-gui::time-picker min="08:00" max="18:00" :interval="15" step="900" required />
</x-gui::field>
```

## In 24-hour time

locale sets how the list writes times; by default it follows the browser. The input itself always follows the browser.

```blade
<x-gui::field label="Departure" name="departure">
    <x-gui::time-picker locale="en-GB" value="18:45" />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `interval` | `int` | `30` | Minutes between the times in the list. |
| `min, max` | `string` | `null` | The earliest and latest times, as H:i. They limit the list and validation. |
| `locale` | `string` | `null` | How the list writes times, e.g. en-US (2:30 PM) or en-GB (14:30). Defaults to the browser's. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. By default it follows the error bag. |
| `button-label` | `string` | `'Choose time'` | The clock button's and the list's accessible name (translatable). |
| `value` | `string \| DateTimeInterface` | `null` | The time, as H:i or a date object. Old input refills it after a failed validation. |

Any other attribute goes on the input: name, required, step, wire:model, x-model.

Choosing from the list fires input and change on the input, so wire:model and x-model update as if it had been typed.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
