# Feature

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A feature for a landing page: an icon on a tile, a title and a sentence or two.

```blade
<x-gui::feature icon="bolt" title="Fast">Pages load in a blink.</x-gui::feature>
```

## A grid of features

heading sets the title's level (h3 by default), to fit your page's outline.

```blade
<div class="grid w-full gap-4 sm:grid-cols-2">
    <x-gui::feature icon="check-badge" title="Accessible by default">Keyboard support, focus management and labels, done for you.</x-gui::feature>
    <x-gui::feature icon="swatch" title="Yours to theme">Change one variable and every component follows.</x-gui::feature>
</div>
```

## Inline and plain

inline puts the icon beside the text, for denser lists. plain drops the panel for an airy grid.

```blade
<div class="grid w-full gap-8 sm:grid-cols-2">
    <div class="space-y-6">
        <x-gui::feature variant="inline" icon="bolt" title="Ready for Livewire">wire:model and loading states work out of the box.</x-gui::feature>
        <x-gui::feature variant="inline" icon="squares-2x2" title="Platform first">Native dialog, popover and form validation.</x-gui::feature>
    </div>
    <div class="grid gap-6 sm:grid-cols-2">
        <x-gui::feature variant="plain" icon="check-badge" title="Accessible">Tested with axe in three browsers.</x-gui::feature>
        <x-gui::feature variant="plain" icon="presentation-chart-line" title="Charts">No chart library needed.</x-gui::feature>
    </div>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `icon` | `string` | `null` | An icon for the tile. |
| `title` | `string` | `` | The feature's name. |
| `heading` | `string` | `'h3'` | The title's element. |
| `variant` | `'tile' \| 'inline' \| 'plain'` | `'tile'` | The layout. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The description. |
