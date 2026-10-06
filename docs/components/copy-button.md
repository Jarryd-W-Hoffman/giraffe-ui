# Copy button

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Copies text to the clipboard and confirms it, with a check for sighted users and "Copied" for screen readers.

```blade
<x-gui::copy-button value="composer require jayaitch/giraffeui" />
```

## Icon only

Named "Copy" for screen readers. Pass the text as value.

```blade
<div class="flex w-full max-w-md items-center justify-between gap-3 rounded-gui border border-zinc-200 bg-zinc-50 py-1 pr-1 pl-3 font-mono text-sm dark:border-zinc-800 dark:bg-zinc-900">
    <code class="truncate">composer require jayaitch/giraffeui</code>
    <x-gui::copy-button value="composer require jayaitch/giraffeui" />
</div>
```

## With a label, copying from the page

target copies the text of an element (or an input's value) by CSS selector, as it is when clicked. The label swaps to "Copied" for two seconds. Takes the Button's variant, color and size.

```blade
<div class="space-y-3">
    <p id="invite-link" class="text-sm">https://acme.app/join/7Hq2-xK9p</p>
    <div class="flex flex-wrap gap-2">
        <x-gui::copy-button target="#invite-link">Copy invite link</x-gui::copy-button>
        <x-gui::copy-button target="#invite-link" variant="solid" color="primary">Copy link</x-gui::copy-button>
    </div>
</div>
```

## In an input

The Input's copyable prop puts one inside the field.

```blade
<x-gui::field label="Webhook URL" name="webhook">
    <x-gui::input value="https://acme.app/hooks/orders/3f9a" readonly copyable />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `value` | `string` | `null` | The text to copy. |
| `target` | `string` | `null` | Or a CSS selector: copies that input's value, or that element's text. |
| `label, copied-label` | `string` | `'Copy', 'Copied'` | The icon button's accessible name, and what it says after a copy (translatable). |
| `variant, color, size` | `string` | `'outline', 'secondary', 'sm'` | As on Button, for a button with a label. Icon only: size xs, sm or md. |

Dispatches a copied event with the text in event.detail.text, e.g. x-on:copied="GiraffeUI.toast('Copied')".

It uses the Clipboard API, falling back to the older copy command on plain HTTP. Hidden until Alpine starts, since it can't work without JavaScript.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | A label. Without one, it's an icon button. |
