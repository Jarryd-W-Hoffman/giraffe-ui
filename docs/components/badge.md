# Badge

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A small label for status, counts or categories.

```blade
<x-gui::badge color="success">Active</x-gui::badge>
```

## Colours

```blade
<x-gui::badge color="primary">Primary</x-gui::badge>
<x-gui::badge>Secondary</x-gui::badge>
<x-gui::badge color="success">Success</x-gui::badge>
<x-gui::badge color="warning">Warning</x-gui::badge>
<x-gui::badge color="danger">Danger</x-gui::badge>
<x-gui::badge color="info">Info</x-gui::badge>
```

## Variants

```blade
<x-gui::badge color="primary">Soft</x-gui::badge>
<x-gui::badge color="primary" variant="solid">Solid</x-gui::badge>
<x-gui::badge color="primary" variant="outline">Outline</x-gui::badge>
```

## With a dot

```blade
<x-gui::badge color="success">
    <svg class="size-1.5 fill-current" viewBox="0 0 6 6" aria-hidden="true"><circle cx="3" cy="3" r="3" /></svg>
    Online
</x-gui::badge>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'soft' \| 'solid' \| 'outline'` | `'soft'` |  |
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'secondary'` |  |
| `size` | `'sm' \| 'md'` | `'md'` |  |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label. |
