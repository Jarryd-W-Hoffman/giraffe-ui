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

## Prefix and suffix

Text attached to either side, for web addresses, currencies and units. Screen readers hear it after the label.

```blade
<div class="space-y-4">
    <x-gui::field label="Web address" name="site">
        <x-gui::input prefix="acme.app/" placeholder="your-shop" />
    </x-gui::field>
    <x-gui::field label="Price" name="price">
        <x-gui::input type="number" prefix="$" suffix="USD" step="0.01" placeholder="0.00" />
    </x-gui::field>
    <x-gui::field label="Weight" name="weight">
        <x-gui::input type="number" suffix="kg" class="max-w-32" />
    </x-gui::field>
</div>
```

## Password with a Show button

viewable adds a button that shows and hides the password. It goes back to hidden when the form submits, so the browser never saves it as text.

```blade
<x-gui::field label="Password" name="password">
    <x-gui::input type="password" viewable autocomplete="current-password" />
</x-gui::field>
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
| `prefix` | `string` | `null` | Text attached before the input, e.g. a domain or currency. |
| `suffix` | `string` | `null` | Text attached after the input, e.g. a unit. |
| `viewable` | `bool` | `false` | For type="password": a button to show and hide it. It replaces the trailing slot. |
| `show-label` | `string` | `'Show password'` | The Show button's accessible name (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `leading` | Content inside the input, before the text. Usually an icon. |
| `trailing` | Content inside the input, after the text. Can be interactive. |
