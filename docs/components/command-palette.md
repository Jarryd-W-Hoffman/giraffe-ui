# Command palette

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Search pages and run actions from the keyboard with ⌘K / Ctrl+K. This catalog has one: try it.

```blade
<x-gui::command-palette>
    <x-gui::command-palette.group heading="Pages">
        <x-gui::command-palette.item href="/dashboard" icon="home">Dashboard</x-gui::command-palette.item>
    </x-gui::command-palette.group>
</x-gui::command-palette>
```

## Pages and actions

Type to filter (keywords count too), arrow keys to move, Enter to go. Items are real links and buttons, so href, wire:click and x-on:click all work. This example turns its shortcut off so it doesn't clash with the catalog's own palette.

```blade
<x-gui::button variant="outline" commandfor="demo-palette" command="show-modal">Open palette</x-gui::button>

<x-gui::command-palette id="demo-palette" :shortcut="null" placeholder="Type a command or search…">
    <x-gui::command-palette.group heading="Pages">
        <x-gui::command-palette.item href="#" icon="home" keywords="overview">Dashboard</x-gui::command-palette.item>
        <x-gui::command-palette.item href="#" icon="folder">Projects</x-gui::command-palette.item>
        <x-gui::command-palette.item href="#" icon="user" keywords="account profile">Settings</x-gui::command-palette.item>
    </x-gui::command-palette.group>
    <x-gui::command-palette.group heading="Actions">
        <x-gui::command-palette.item icon="plus" shortcut="⌘ N" x-on:click="GiraffeUI.toast('New project created', { color: 'success' })">New project</x-gui::command-palette.item>
        <x-gui::command-palette.item icon="arrow-right" x-on:click="GiraffeUI.toast('Signed out')">Sign out</x-gui::command-palette.item>
    </x-gui::command-palette.group>
</x-gui::command-palette>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `id` | `string` | `'command-palette'` | Used to open it with commandfor. |
| `shortcut` | `string \| null` | `'k'` | Opens with ⌘ or Ctrl plus this key. null turns it off. |
| `placeholder` | `string` | `'Search…'` | Also the search box's accessible name. |
| `label` | `string` | `'Command palette'` | The dialog's accessible name. |
| `empty` | `string` | `'No results'` |  |
| `heading (group)` | `string` | `required` | Labels the group. |
| `href (item)` | `string` | `null` | Makes the item a link. |
| `icon (item)` | `string` | `null` | An icon name. |
| `keywords (item)` | `string` | `null` | Extra words that match the search. |
| `shortcut (item)` | `string` | `null` | Keys to show, separated by spaces, e.g. "⌘ N". Display only. |

Needs Alpine (Livewire includes it). Put one in your layout so it is available everywhere.

Focus stays in the search box; aria-activedescendant points at the highlighted item.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | Groups, or items directly. |
