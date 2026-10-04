# Kbd

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A keyboard key, for shortcuts and instructions.

```blade
<x-gui::kbd>⌘</x-gui::kbd> <x-gui::kbd>K</x-gui::kbd>
```

## Shortcuts

```blade
<p class="text-sm text-zinc-600 dark:text-zinc-400">
    Press <x-gui::kbd>⌘</x-gui::kbd> <x-gui::kbd>K</x-gui::kbd> to search, or <x-gui::kbd>Esc</x-gui::kbd> to close.
</p>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The key. |
