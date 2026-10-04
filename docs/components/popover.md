# Popover

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Rich content in a floating panel. A native popover, so Escape and clicking outside close it without JavaScript.

```blade
<x-gui::popover id="share" label="Share">
    …
</x-gui::popover>
```

## Share

```blade
<x-gui::popover id="share" label="Share">
    <div class="w-72 space-y-3">
        <p class="font-medium">Share this project</p>
        <x-gui::input value="https://giraffeui.dev/p/8f3k" readonly aria-label="Link" />
        <x-gui::button size="sm" class="w-full" x-on:click="GiraffeUI.toast('Link copied', { color: 'success' })">Copy link</x-gui::button>
    </div>
</x-gui::popover>
```

## Positions

```blade
<x-gui::popover id="pop-top" label="Top" position="top">Above the trigger.</x-gui::popover>
<x-gui::popover id="pop-right" label="Right" position="right">Beside the trigger.</x-gui::popover>
<x-gui::popover id="pop-bottom-end" label="Bottom end" position="bottom-end">Below, aligned to the end.</x-gui::popover>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `id` | `string` | `required` | Used by the trigger to open it. |
| `label` | `string` | `null` | Text for the default trigger button. |
| `position` | `'bottom-start' \| 'bottom' \| 'bottom-end' \| 'top-start' \| 'top' \| 'top-end' \| 'left' \| 'right'` | `'bottom-start'` | Flips automatically when there is no room. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `trigger` | A custom trigger. Give it commandfor="{id}" command="toggle-popover". |
| `default` | The panel content. |
