# Combobox

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A select you can type into to filter. Follows the WAI-ARIA combobox pattern. Needs Alpine, which Livewire includes.

```blade
<x-gui::combobox name="country" :options="$countries" placeholder="Search countries" />
```

## In a field

Type to filter. Arrow keys move through the list, Enter picks, Escape closes the list (and clears the value when it is already closed). The value is submitted through a hidden input named after the field.

```blade
<x-gui::field label="Country" name="country" hint="Where you're based.">
    <x-gui::combobox :options="$countries" placeholder="Search countries" />
</x-gui::field>
```

## With a value

```blade
<x-gui::field label="Role" name="role">
    <x-gui::combobox :options="$roles" value="editor" />
</x-gui::field>
```

## In a modal

The list is a top-layer popover, so it is never clipped by a modal or a scrolling container.

```blade
<x-gui::button variant="outline" commandfor="move-project" command="show-modal">Move project</x-gui::button>

<x-gui::modal id="move-project" title="Move project" size="sm">
    <x-gui::field label="Region" name="region">
        <x-gui::combobox :options="$countries" placeholder="Search regions" />
    </x-gui::field>
    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button commandfor="move-project" command="close">Move</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `options` | `array` | `[]` | value =&gt; label pairs. |
| `value` | `string` | `null` | The selected value. Defaults to old input. |
| `placeholder` | `string` | `null` |  |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |
| `empty` | `string` | `'No results'` | Shown when nothing matches. |

wire:model and x-model bind the value; other attributes go on the text input. The selected value fires a change event on the hidden input.

Options are filtered in the browser. For very large lists, search on the server and pass fewer options.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
