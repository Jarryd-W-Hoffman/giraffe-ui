# Sidebar

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

An application shell with side navigation: a sticky column on large screens and a drawer on small ones.

```blade
<x-gui::sidebar-layout>
    <x-slot:brand>Acme</x-slot:brand>
    <x-slot:sidebar>
        <x-gui::sidebar>
            <x-gui::sidebar.group>
                <x-gui::sidebar.item href="/dashboard" icon="home">Dashboard</x-gui::sidebar.item>
            </x-gui::sidebar.group>
        </x-gui::sidebar>
    </x-slot:sidebar>
    <x-slot:header>…</x-slot:header>

    {{ $slot }}
</x-gui::sidebar-layout>
```

## Navigation

A labelled nav of groups and links. The link for the page being shown gets aria-current="page" automatically, or set current yourself. Group headings name their group without an id, so the layout can render the same navigation in its column and its drawer. See it all together in the Dashboard example.

```blade
<div class="rounded-gui border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900">
    <x-gui::sidebar label="Example">
        <x-gui::sidebar.group>
            <x-gui::sidebar.item href="#" icon="home" :current="true">Dashboard</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#" icon="folder" badge="3">Projects</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#" icon="user">Customers</x-gui::sidebar.item>
        </x-gui::sidebar.group>
        <x-gui::sidebar.group heading="Help">
            <x-gui::sidebar.item href="/catalog/introduction" icon="information-circle">Documentation</x-gui::sidebar.item>
        </x-gui::sidebar.group>
    </x-gui::sidebar>
</div>
<x-gui::button href="/catalog/examples/dashboard" variant="outline" color="secondary">Open the Dashboard example →</x-gui::button>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `sidebar-layout: drawer-id` | `string` | `'gui-sidebar-drawer'` | Id of the drawer used on small screens. |
| `sidebar-layout: drawer-title` | `string` | `'Navigation'` | The drawer's title. |
| `sidebar-layout: menu-label` | `string` | `'Open navigation'` | The menu button's accessible name. |
| `sidebar: label` | `string` | `'Main'` | The nav's accessible name. |
| `sidebar.group: heading` | `string` | `null` | A heading that also names the group. |
| `sidebar.item: href` | `string` | `` | Where the link goes. |
| `sidebar.item: icon` | `string` | `null` | An icon name. |
| `sidebar.item: current` | `bool \| null` | `null` | Whether this is the current page. Defaults to comparing href with the current URL. |
| `sidebar.item: badge` | `string` | `null` | A count or label after the text. |

The drawer is the Drawer component, a native modal dialog: focus is trapped, Escape closes it, and focus returns to the menu button.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `sidebar-layout: brand` | Logo or name, at the top of the column and in the bar on small screens. |
| `sidebar-layout: sidebar` | The navigation, usually x-gui::sidebar. Rendered in the column and the drawer. |
| `sidebar-layout: footer` | Pinned to the bottom of the column, e.g. the signed-in user. |
| `sidebar-layout: header` | The top bar's contents, e.g. search and an account menu. |
| `sidebar-layout: default` | The page. |
