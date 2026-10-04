# Skeleton

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A placeholder shaped like the content that is loading.

```blade
<x-gui::skeleton class="h-4 w-48" />
```

## Card placeholder

Skeletons are hidden from screen readers. Mark the loading region with aria-busy="true" instead.

```blade
<div aria-busy="true" class="mx-auto flex max-w-sm items-start gap-4">
    <x-gui::skeleton class="size-10 shrink-0 rounded-full!" />
    <div class="flex-1 space-y-3">
        <x-gui::skeleton class="h-4 w-1/2" />
        <x-gui::skeleton :lines="3" />
    </div>
</div>
```

## With Livewire

Show skeletons while a lazy component loads, or with wire:loading.

```blade
<div class="mx-auto max-w-sm space-y-2">
    <x-gui::skeleton class="h-8 w-full" />
    <x-gui::skeleton class="h-8 w-full" />
    <x-gui::skeleton class="h-8 w-full" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `lines` | `int` | `null` | Render this many text lines, the last one shorter. |

Size and shape it with classes. With reduced motion it stops pulsing.

In a Livewire component, return skeletons from placeholder() for lazy loading.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
