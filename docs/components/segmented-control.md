# Segmented control

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A row of options where one is always picked, like a view switcher. Native radios underneath, so no JavaScript.

```blade
<x-gui::segmented-control name="view" label="View" :options="['list' => 'List', 'grid' => 'Grid']" value="list" />
```

## In a form

Tab reaches the picked option, and arrow keys change it. It submits like any radio group.

```blade
<x-gui::segmented-control name="billing" label="Billing period" :options="['monthly' => 'Monthly', 'yearly' => 'Yearly']" value="yearly" />
```

## Full width

```blade
<x-gui::segmented-control name="theme" label="Theme" :options="['light' => 'Light', 'dark' => 'Dark', 'system' => 'System']" value="system" full />
```

## With a visible label

Point aria-labelledby at your own text instead of passing label.

```blade
<div class="flex items-center gap-3">
    <span id="density-label" class="text-sm font-medium">Density</span>
    <x-gui::segmented-control name="density" aria-labelledby="density-label" size="sm" :options="['compact' => 'Compact', 'comfortable' => 'Comfortable']" value="comfortable" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `options` | `array` | `[]` | value =&gt; label pairs. |
| `name` | `string` | `null` | The radios' name. Defaults to the wire:model property. |
| `value` | `string` | `null` | The picked value. Defaults to old input. |
| `label` | `string` | `null` | The group's accessible name. Or use aria-labelledby. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `full` | `bool` | `false` | Stretch to the container, with equal-width options. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

wire:model, x-model, disabled, required and form go on every radio. Other attributes go on the group.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
