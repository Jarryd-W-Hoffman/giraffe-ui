# Avatar group

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Overlapping avatars, with a count of the ones not shown.

```blade
<x-gui::avatar.group :avatars="$team" :max="4" label="Team" />
```

## From an array

Each item takes name, src and alt. Avatars past max become a +N count, which screen readers hear as "N more".

```blade
<x-gui::avatar.group :avatars="$users" :max="3" label="Reviewers" />
```

## In the slot

Write the avatars yourself and pass more for the count, e.g. from a separate query.

```blade
<x-gui::avatar.group size="sm" more="12" label="Attendees">
    <x-gui::avatar :src="$photo" name="Ada Lovelace" size="sm" />
    <x-gui::avatar name="Grace Hopper" size="sm" />
</x-gui::avatar.group>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `avatars` | `array \| null` | `null` | Items with name, src and alt. Or use the slot. |
| `max` | `int \| null` | `null` | How many avatars to show before the count. |
| `more` | `int` | `0` | Extra people to count, on top of those past max. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | For avatars from the array and the count. In the slot, size each avatar. |
| `label` | `string` | `null` | Makes it a labelled group for screen readers. |
| `more-label` | `string` | `':count more'` | The count's accessible name. |

Each avatar has a ring in the page colour. On another background, change it with e.g. class="*:ring-zinc-50!".

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | Avatars, when not passing avatars. |
