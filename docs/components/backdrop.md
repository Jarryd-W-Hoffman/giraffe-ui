# Backdrop

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Decoration for behind a hero: a glow, a grid, dots, an aurora or a spotlight. Hidden from screen readers.

```blade
<section class="relative isolate overflow-hidden"><x-gui::backdrop /> …</section>
```

## Behind a hero

It fills its section from behind: give the section relative isolate overflow-hidden. The default is a glow over a fine grid.

```blade
<section class="relative isolate overflow-hidden rounded-3xl px-6 py-20 text-center">
    <x-gui::backdrop />
    <h2 class="text-4xl font-normal tracking-tight">Build something <span class="text-zinc-500 dark:text-zinc-400">lovely</span></h2>
</section>
```

## Variants

grid on its own, dots, aurora (drifting colour) and spotlight (a pool of light from the top).

```blade
<div class="grid w-full gap-4 sm:grid-cols-2">
    @foreach (['grid' => 'Grid', 'dots' => 'Dots', 'aurora' => 'Aurora', 'spotlight' => 'Spotlight'] as $variant => $name)
        <section class="relative isolate overflow-hidden rounded-3xl px-6 py-16 text-center ring-1 ring-zinc-200 dark:ring-zinc-800">
            <x-gui::backdrop :variant="$variant" />
            <p class="text-2xl font-normal tracking-tight">{{ $name }}</p>
        </section>
    @endforeach
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'glow' \| 'grid' \| 'dots' \| 'aurora' \| 'spotlight'` | `'glow'` | The style of decoration. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
