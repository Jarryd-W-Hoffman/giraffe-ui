# Progress

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Shows how far along a task is, or that it is working when you can't tell.

```blade
<x-gui::progress :value="$percent" label="Uploading" />
```

## Determinate

```blade
<x-gui::progress :value="64" label="Uploading photos" show-value />
<x-gui::progress :value="3" :max="4" label="Step 3 of 4" color="success" />
```

## Indeterminate

Leave out value when you can't measure progress.

```blade
<x-gui::progress label="Preparing your export" />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `value` | `number` | `null` | Progress so far. Leave it out for indeterminate. |
| `max` | `number` | `100` |  |
| `label` | `string` | `null` | Shown above the bar and used as its accessible name. |
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'primary'` |  |
| `show-value` | `bool` | `false` | Show the percentage. |

Always give it a label, or an aria-label, so screen readers know what is progressing.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
