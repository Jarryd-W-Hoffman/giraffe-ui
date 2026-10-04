# Input

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A text input that works on its own or inside a field.

```blade
<x-gui::input name="email" type="email" />
```

## Sizes

```blade
<x-gui::input size="sm" placeholder="Small" />
<x-gui::input size="md" placeholder="Medium" />
<x-gui::input size="lg" placeholder="Large" />
```

## With icons

Leading content is decorative. Trailing content can be interactive, like a button.

```blade
<x-gui::input type="search" placeholder="Search" aria-label="Search">
    <x-slot:leading>
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
    </x-slot:leading>
</x-gui::input>

<div x-data="{ show: false }">
    <x-gui::input x-bind:type="show ? 'text' : 'password'" value="hunter2" aria-label="Password">
        <x-slot:trailing>
            <button type="button" class="text-xs font-medium hover:text-zinc-900 dark:hover:text-zinc-100" x-on:click="show = ! show" x-text="show ? 'Hide' : 'Show'">Show</button>
        </x-slot:trailing>
    </x-gui::input>
</div>
```

## Disabled

```blade
<x-gui::input value="Can't touch this" disabled aria-label="Disabled example" />
```

## File

```blade
<x-gui::input type="file" aria-label="Upload a file" />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `type` | `string` | `'text'` | Any input type. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | Height and text size. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. By default it follows the error bag. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `leading` | Content inside the input, before the text. Usually an icon. |
| `trailing` | Content inside the input, after the text. Can be interactive. |
