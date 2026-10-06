# Theme switcher

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Light, dark or the system's setting, remembered in this browser. Every switcher on the page stays in sync.

```blade
<x-gui::theme-switcher />
```

## Light, dark or system

Native radios: one tab stop, and arrow keys switch. System follows the operating system, even as it changes. Try it: it switches this page.

```blade
<x-gui::theme-switcher />
```

## Icons only, or one toggle button

Without labels, each icon is still named for screen readers. The toggle switches between light and dark and reports its state with aria-pressed; the catalog's header uses it.

```blade
<div class="flex items-center gap-4">
    <x-gui::theme-switcher :labels="false" size="sm" />
    <x-gui::theme-switcher variant="toggle" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'segmented' \| 'toggle'` | `'segmented'` | Three choices, or a single light/dark button. |
| `labels` | `bool` | `true` | Show the words next to the icons (segmented). |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `label` | `string` | `'Theme' / 'Dark mode'` | The accessible name (translatable). |

It calls GiraffeUI.setTheme() from the published layout, which also applies the saved theme before the page paints. Without that layout it defines setTheme itself, but put the layout's head script in yours to avoid a flash on load.

The choice is stored in localStorage as theme (light or dark; system clears it), and the page gets a dark class, matching Tailwind's class-based dark mode set up by the installer.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
