# Select

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native select, styled to match the other controls.

```blade
<x-gui::select name="role" :options="$roles" placeholder="Choose a role" />
```

## Options from an array

Pass value =&gt; label pairs. The value prop (or old input) is selected.

```blade
<x-gui::field label="Role" name="role">
    <x-gui::select :options="$roles" placeholder="Choose a role" />
</x-gui::field>
```

## Options in the slot

Write your own options and optgroups.

```blade
<x-gui::field label="Timezone" name="timezone">
    <x-gui::select>
        <optgroup label="Europe">
            <option>Europe/London</option>
            <option>Europe/Paris</option>
        </optgroup>
        <optgroup label="Oceania">
            <option selected>Australia/Sydney</option>
            <option>Pacific/Auckland</option>
        </optgroup>
    </x-gui::select>
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `options` | `iterable` | `null` | value =&gt; label pairs. |
| `placeholder` | `string` | `null` | A disabled first option, selected when there is no value. |
| `value` | `string \| array` | `null` | The selected value. Defaults to old input. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | Height and text size. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | Extra option and optgroup elements. |
