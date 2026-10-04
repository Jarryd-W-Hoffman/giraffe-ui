# Separator

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A line that divides content, optionally with a label.

```blade
<x-gui::separator />
```

## With a label

```blade
<div class="mx-auto max-w-sm space-y-4">
    <x-gui::button variant="outline" color="secondary" class="w-full">Continue with GitHub</x-gui::button>
    <x-gui::separator label="or" />
    <x-gui::input type="email" placeholder="you@example.com" aria-label="Email" />
</div>
```

## Vertical

```blade
<div class="flex h-6 items-center gap-4 text-sm">
    <a href="#">Docs</a>
    <x-gui::separator orientation="vertical" />
    <a href="#">Blog</a>
    <x-gui::separator orientation="vertical" />
    <a href="#">Changelog</a>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `orientation` | `'horizontal' \| 'vertical'` | `'horizontal'` |  |
| `label` | `string` | `null` | Text in the middle of a horizontal line. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.
