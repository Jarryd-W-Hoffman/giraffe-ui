# Spinner

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Shows that something is loading. Buttons use it for their loading state.

```blade
<x-gui::spinner label="Loading results" />
```

## Sizes

```blade
<x-gui::spinner size="sm" />
<x-gui::spinner />
<x-gui::spinner size="lg" class="text-gui-primary-text" />
```

## Announced

Give it a label when it stands alone, so screen readers hear it. Without one it's decorative.

```blade
<div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
    <x-gui::spinner size="sm" label="Loading results" />
    Loading results…
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `label` | `string` | `null` | Announced as a status. Without it the spinner is aria-hidden. |

It inherits the text colour. With reduced motion it spins more slowly rather than stopping, since it is the only sign of progress.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.
