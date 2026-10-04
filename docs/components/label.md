# Label

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A form label. Field uses it for you; use it directly for custom layouts.

```blade
<x-gui::label for="email">Email</x-gui::label>
```

## Required

The asterisk is hidden from screen readers; put required on the control so they announce it.

```blade
<x-gui::label for="company" required>Company</x-gui::label>
<x-gui::input id="company" name="company" required />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `required` | `bool` | `false` | Shows a required marker. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label text. |
