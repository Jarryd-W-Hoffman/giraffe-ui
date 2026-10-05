# Navbar

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A top bar with your brand, links and actions. Below the md breakpoint the links fold into a menu, a native popover that needs no JavaScript.

```blade
<x-gui::navbar>
    <x-slot:brand>…</x-slot:brand>
    <x-gui::navbar.item href="/pricing">Pricing</x-gui::navbar.item>
    <x-slot:actions>…</x-slot:actions>
</x-gui::navbar>
```

## Brand, links and actions

The current page is marked with aria-current="page", worked out from the URL unless you pass current. Narrow the window to see the menu button; the actions stay in the bar.

```blade
<x-gui::navbar>
    <x-slot:brand>
        <a href="#" class="flex items-center gap-2 font-semibold">
            <span class="flex size-7 items-center justify-center rounded-gui bg-gui-primary text-sm text-gui-primary-foreground" aria-hidden="true">A</span>
            Acme
        </a>
    </x-slot:brand>

    <x-gui::navbar.item href="#" current>Product</x-gui::navbar.item>
    <x-gui::navbar.item href="#">Pricing</x-gui::navbar.item>
    <x-gui::navbar.item href="#">Docs</x-gui::navbar.item>

    <x-slot:actions>
        <x-gui::button href="#" variant="ghost" color="secondary" size="sm">Sign in</x-gui::button>
        <x-gui::button href="#" size="sm">Get started</x-gui::button>
    </x-slot:actions>
</x-gui::navbar>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `'Main'` | Names the navigation landmark (translatable). |
| `menu-label` | `string` | `'Menu'` | The small-screen menu button's accessible name (translatable). |
| `sticky` | `bool` | `false` | Keep the bar at the top of the window, over a blurred background. |
| `container` | `string` | `'max-w-6xl'` | Width classes for the bar's contents, e.g. max-w-7xl. |
| `href (item)` | `string` | `required` |  |
| `current (item)` | `bool \| null` | `null` | Mark the item as the current page. By default it is when its URL is the page being shown. |

The links are rendered twice, in the bar and in the menu, with one hidden by CSS at each size; actions are rendered once.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `brand` | Your logo or name, on the left. |
| `default` | navbar.item links. |
| `actions` | Buttons or a dropdown on the right, shown at every size. |
