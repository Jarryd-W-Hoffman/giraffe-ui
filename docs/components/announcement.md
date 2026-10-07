# Announcement

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A small pill that links to something new, for the top of a landing page or a changelog.

```blade
<x-gui::announcement href="/changelog">Charts are here</x-gui::announcement>
```

## Basic

badge sets the label in front (or false for none). Screen readers hear it with the text: "New: Charts are here". variant="subtle" drops the pill.

```blade
<div class="flex flex-col items-center gap-3">
    <x-gui::announcement href="#">Charts, a collapsible sidebar and 167 icons</x-gui::announcement>
    <x-gui::announcement href="#" badge="Beta">Try the new theme editor</x-gui::announcement>
    <x-gui::announcement href="#" variant="subtle">Version 0.2 is out</x-gui::announcement>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `href` | `string` | `` | Where it links. |
| `badge` | `string \| false` | `'New'` | The label in front. |
| `variant` | `'pill' \| 'subtle'` | `'pill'` | A ringed pill, or just the badge and the words. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The announcement. |
