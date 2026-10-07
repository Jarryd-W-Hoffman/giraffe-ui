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

## Pills with a dot

dot puts a small coloured mark before the label, from the theme (success) or any CSS colour. The label says what it means; the dot only helps scanning.

```blade
<div class="flex flex-wrap items-center gap-2">
    <x-gui::badge shape="pill" dot="success">Paid</x-gui::badge>
    <x-gui::badge shape="pill" dot="warning">Pending</x-gui::badge>
    <x-gui::badge shape="pill" dot="var(--color-violet-500)">Transfer</x-gui::badge>
    <x-gui::badge shape="pill" dot="var(--color-lime-500)">Hosting</x-gui::badge>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'soft' \| 'solid' \| 'outline'` | `'soft'` |  |
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'secondary'` |  |
| `size` | `'sm' \| 'md'` | `'md'` |  |
| `shape` | `'default' \| 'pill'` | `'default'` | pill: fully rounded ends. |
| `dot` | `string` | `null` | A small coloured dot before the label: a theme colour (success) or any CSS colour. Decoration only. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The label. |
