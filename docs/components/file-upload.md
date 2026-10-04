# File upload

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A drop zone around a real file input, with the chosen files listed and Livewire upload progress.

```blade
<x-gui::file-upload name="avatar" accept="image/*" />
```

## In a field

Click, press Enter or Space, or drag files in. It is a real file input, so it works without JavaScript too.

```blade
<x-gui::field label="Avatar" name="avatar">
    <x-gui::file-upload accept="image/*" hint="PNG or JPG, up to 2 MB." />
</x-gui::field>
```

## Multiple files

```blade
<x-gui::field label="Attachments" name="attachments[]">
    <x-gui::file-upload multiple />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `hint` | `string` | `null` | Shown inside the drop zone, e.g. allowed types and size. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

accept, multiple, wire:model and other attributes go on the input. With Livewire, a progress bar shows while the file uploads; see the Livewire guide for a live demo.

Browsers don't apply accept to dropped files, so validate on the server as usual.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
