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

A labelled nav of groups and links. The link for the page being shown gets aria-current="page" automatically, or set current yourself. Group headings name their group without an id, so the layout can render the same navigation in its column and its drawer. See it all together in the Settings example.

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
<x-gui::button href="/catalog/examples/settings" variant="outline" color="secondary">Open the Settings example →</x-gui::button>
```

## Sub-sections

sidebar.submenu nests items under a parent, behind a native disclosure that works without JavaScript. It opens by itself when it holds the current page. collapsible on a group lets its heading show and hide it.

```blade
<div class="w-64 rounded-gui border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900">
    <x-gui::sidebar label="Sub-sections example">
        <x-gui::sidebar.group>
            <x-gui::sidebar.item href="#" icon="home">Dashboard</x-gui::sidebar.item>
            <x-gui::sidebar.submenu label="Projects" icon="folder" badge="3">
                <x-gui::sidebar.item href="#">Website redesign</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#" :current="true">Mobile app</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#">Spring campaign</x-gui::sidebar.item>
            </x-gui::sidebar.submenu>
            <x-gui::sidebar.submenu label="Reports" icon="calendar">
                <x-gui::sidebar.item href="#">Weekly</x-gui::sidebar.item>
                <x-gui::sidebar.item href="#">Monthly</x-gui::sidebar.item>
            </x-gui::sidebar.submenu>
        </x-gui::sidebar.group>
        <x-gui::sidebar.group heading="Settings" collapsible>
            <x-gui::sidebar.item href="#" icon="user">Profile</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#" icon="cog-6-tooth">Preferences</x-gui::sidebar.item>
        </x-gui::sidebar.group>
    </x-gui::sidebar>
</div>
```

## Collapsing to a rail

collapsible on the layout adds a Collapse sidebar button below the navigation. Collapsed, the column becomes a rail of icons: hover or focus one for its name, and screen readers still hear every label. Items without an icon show their first letter. The choice is remembered in this browser and applied before the page paints. Below, a rail on its own (data-collapsed is what the button sets); the Settings example has the real thing.

```blade
<div data-collapsed class="w-16 rounded-gui border border-zinc-200 bg-zinc-50 px-3 py-3 dark:border-zinc-800 dark:bg-zinc-900">
    <x-gui::sidebar label="Rail example">
        <x-gui::sidebar.group>
            <x-gui::sidebar.item href="#" icon="home" :current="true">Dashboard</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#" icon="folder" badge="3">Projects</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#" icon="user">Customers</x-gui::sidebar.item>
        </x-gui::sidebar.group>
        <x-gui::sidebar.group heading="Help">
            <x-gui::sidebar.item href="#" icon="information-circle">Documentation</x-gui::sidebar.item>
            <x-gui::sidebar.item href="#">Changelog</x-gui::sidebar.item>
        </x-gui::sidebar.group>
    </x-gui::sidebar>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `sidebar-layout: drawer-id` | `string` | `'gui-sidebar-drawer'` | Id of the drawer used on small screens. |
| `sidebar-layout: drawer-title` | `string` | `'Navigation'` | The drawer's title. |
| `sidebar-layout: menu-label` | `string` | `'Open navigation'` | The menu button's accessible name. |
| `sidebar-layout: collapsible` | `bool` | `false` | A button that collapses the column to a rail of icons, from the lg breakpoint. |
| `sidebar-layout: collapsed` | `bool` | `false` | Start collapsed, until the visitor chooses. |
| `sidebar-layout: persist` | `string \| null` | `'gui-sidebar-collapsed'` | The localStorage key that remembers the choice. null remembers nothing. |
| `sidebar-layout: collapse-label, expand-label` | `string` | `'Collapse sidebar', 'Expand sidebar'` | The button's text (translatable). |
| `sidebar: label` | `string` | `'Main'` | The nav's accessible name. |
| `sidebar.group: heading` | `string` | `null` | A heading that also names the group. |
| `sidebar.group: collapsible, open` | `bool` | `false, true` | Let the heading show and hide the group, and whether it starts open. It opens anyway when it holds the current page. |
| `sidebar.submenu: label, icon, badge` | `string` | `` | The parent item: its text, icon and badge. |
| `sidebar.submenu: open` | `bool \| null` | `null` | Whether it starts open. Defaults to open when it holds the current page. |
| `sidebar.item: href` | `string` | `` | Where the link goes. |
| `sidebar.item: icon` | `string` | `null` | An icon name. |
| `sidebar.item: current` | `bool \| null` | `null` | Whether this is the current page. Defaults to comparing href with the current URL. |
| `sidebar.item: badge` | `string` | `null` | A count or label after the text. |

The drawer is the Drawer component, a native modal dialog: focus is trapped, Escape closes it, and focus returns to the menu button.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `sidebar-layout: brand` | Logo or name, at the top of the column and in the bar on small screens. In the rail only its start shows, so lead with a logo. |
| `sidebar-layout: sidebar` | The navigation, usually x-gui::sidebar. Rendered in the column and the drawer. |
| `sidebar-layout: footer` | Pinned to the bottom of the column, e.g. the signed-in user. In the rail only its start shows, so lead with an avatar. |
| `sidebar.submenu: default` | The nested sidebar.item elements. |
| `sidebar-layout: header` | The top bar's contents, e.g. search and an account menu. |
| `sidebar-layout: default` | The page. |
