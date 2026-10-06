# Rating

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Pick a number of stars, or show a rating. A native radio group underneath, so it needs no JavaScript.

```blade
<x-gui::rating name="stars" />
```

## In a form

Tab reaches the stars, arrow keys change the rating, and each star is read out as "3 stars". Pointing at a star previews it. It submits like any radio group, and required works.

```blade
<form x-on:submit.prevent="GiraffeUI.toast('Thanks for the ' + new FormData($el).get('stars') + ' stars!', { color: 'success' })" class="space-y-4">
    <x-gui::field label="How was your order?" name="stars" required>
        <x-gui::rating />
    </x-gui::field>
    <x-gui::button type="submit" size="sm">Send review</x-gui::button>
</form>
```

## Showing a rating

With readonly it only shows the value, filling part of a star for fractions, and screen readers hear "Rated 4.5 out of 5".

```blade
<div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
    <x-gui::rating readonly :value="4.5" size="sm" />
    <span>4.5 from 128 reviews</span>
</div>
```

## Sizes, more stars and disabled

```blade
<div class="space-y-3">
    <x-gui::rating name="small" size="sm" value="2" aria-label="Small" />
    <x-gui::rating name="large" size="lg" :max="10" value="7" aria-label="Out of ten" />
    <x-gui::rating name="locked" value="3" disabled aria-label="Locked" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `max` | `int` | `5` | How many stars. |
| `value` | `int \| float` | `null` | The rating. Defaults to old input. |
| `readonly` | `bool` | `false` | Show the value (fractions too) instead of picking. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

name, wire:model, x-model, required and disabled go on every radio; class and aria-* go on the group. In a field, the group is named by the field's label.

The star texts ("3 stars", "Rated 4.5 out of 5") are translatable.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
