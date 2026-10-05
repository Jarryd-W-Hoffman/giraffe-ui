# Combobox

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A select you can type into to filter, in the browser or on the server. Follows the WAI-ARIA combobox pattern. Needs Alpine, which Livewire includes.

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

## Searching on the server

For long lists, give url. Typing fetches url?q=… (after a short pause) and shows the options the server sends back, so only matches cross the wire. options are the ones shown before anything is typed. This searches PHP's 400+ timezones with an ordinary Laravel route: no Livewire needed.

```blade
<x-gui::field label="Timezone" name="timezone" hint="Try Tokyo or America.">
    <x-gui::combobox url="/catalog/api/timezones" :options="$timezones" placeholder="Search timezones" />
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
| `url` | `string` | `null` | Search on the server: typing fetches url?q=… and shows the JSON it returns. |
| `debounce` | `int` | `300` | Milliseconds to wait after typing before searching. |
| `searching-text` | `string` | `'Searching…'` | Shown while a search runs. |

wire:model and x-model bind the value; other attributes go on the text input. The selected value fires a change event on the hidden input.

Without url, options are filtered in the browser.

With url, return a JSON list of {"value", "label"} objects, e.g. Route::get('/timezones', fn (Request $request) =&gt; Timezone::where('name', 'like', "%{$request-&gt;q}%")-&gt;limit(20)-&gt;get(['id as value', 'name as label'])). A {"value": "label"} object works too, but JavaScript reorders numeric keys, so prefer the list.

While searching, the list shows "Searching…" and is aria-busy. Enter straight after typing picks the first result once it arrives. A newer search cancels an older one. Closing the list goes back to options.

The selected option keeps its label even when it isn't among the current results. In Livewire, wire:model binds the value as usual.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
