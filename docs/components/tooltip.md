# Tooltip

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A short label shown on hover and keyboard focus. Escape dismisses it, and you can hover over it.

```blade
<x-gui::tooltip text="Copy to clipboard">
    <x-gui::button aria-label="Copy">…</x-gui::button>
</x-gui::tooltip>
```

## Icon buttons

Tooltips describe; they don't name. Icon-only buttons still need an aria-label.

```blade
<x-gui::tooltip text="Copy to clipboard">
    <x-gui::button variant="outline" color="secondary" size="sm" aria-label="Copy" class="w-8 px-0!">
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
    </x-gui::button>
</x-gui::tooltip>

<x-gui::tooltip text="Opens in a new tab" position="bottom">
    <x-gui::button href="https://laravel.com" external variant="ghost" size="sm">Laravel</x-gui::button>
</x-gui::tooltip>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `text` | `string` | `required` | The tooltip text. |
| `position` | `'top' \| 'bottom' \| 'left' \| 'right' \| …` | `'top'` | Flips automatically when there is no room. |

Showing and hiding is CSS. Alpine adds aria-describedby and Escape-to-dismiss.

Touch screens can't hover, so never put essential information only in a tooltip.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The trigger. Its first focusable element gets aria-describedby. |
