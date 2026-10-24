# Dropdown

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A menu of actions, options and submenus. A native popover positioned with CSS anchor positioning, with the menu keyboard pattern added by Alpine.

```blade
<x-gui::dropdown id="project-actions" label="Options">
    <x-gui::dropdown.item wire:click="edit">Edit</x-gui::dropdown.item>
    <x-gui::dropdown.item color="danger" wire:click="delete">Delete</x-gui::dropdown.item>
</x-gui::dropdown>
```

## Actions

Opening focuses the first item. Arrow keys, Home and End move between items; Escape, Tab or clicking outside closes it and returns focus.

```blade
<x-gui::dropdown id="project-actions" label="Options">
    <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Editing…')">Edit</x-gui::dropdown.item>
    <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Duplicated', { color: 'success' })">Duplicate</x-gui::dropdown.item>
    <x-gui::dropdown.item href="#">Settings</x-gui::dropdown.item>
    <x-gui::dropdown.item disabled>Archive</x-gui::dropdown.item>
    <x-gui::dropdown.separator />
    <x-gui::dropdown.item color="danger" commandfor="confirm-delete" command="show-modal">Delete…</x-gui::dropdown.item>
</x-gui::dropdown>

<x-gui::modal id="confirm-delete" title="Delete project?" size="sm">
    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button color="danger" commandfor="confirm-delete" command="close">Delete</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Checkbox and radio items

They are real checkboxes and radios with menu roles, so they toggle natively, submit with a form and work with wire:model or x-model. Space, Enter or a click toggles one and leaves the menu open. Groups name their set for screen readers.

```blade
<x-gui::dropdown id="view-options" label="View">
    <x-gui::dropdown.group heading="Show">
        <x-gui::dropdown.checkbox name="show[]" value="drafts" checked>Drafts</x-gui::dropdown.checkbox>
        <x-gui::dropdown.checkbox name="show[]" value="archived">Archived projects</x-gui::dropdown.checkbox>
    </x-gui::dropdown.group>
    <x-gui::dropdown.separator />
    <x-gui::dropdown.group heading="Sort by">
        <x-gui::dropdown.radio name="sort" value="name" checked>Name</x-gui::dropdown.radio>
        <x-gui::dropdown.radio name="sort" value="updated">Last updated</x-gui::dropdown.radio>
        <x-gui::dropdown.radio name="sort" value="created">Date created</x-gui::dropdown.radio>
    </x-gui::dropdown.group>
</x-gui::dropdown>
```

## Submenus

A submenu is a nested popover beside its item. ArrowRight, Enter or a click opens it on its first item, ArrowLeft or Escape goes back, and hovering opens it too. Picking an item closes every level.

```blade
<x-gui::dropdown id="file-menu" label="File">
    <x-gui::dropdown.item x-on:click="GiraffeUI.toast('New file')">New file</x-gui::dropdown.item>
    <x-gui::dropdown.submenu label="Share">
        <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Link copied', { color: 'success' })">Copy link</x-gui::dropdown.item>
        <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Email drafted')">Email</x-gui::dropdown.item>
        <x-gui::dropdown.submenu label="Export as">
            <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Exported as PDF')">PDF</x-gui::dropdown.item>
            <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Exported as CSV')">CSV</x-gui::dropdown.item>
        </x-gui::dropdown.submenu>
    </x-gui::dropdown.submenu>
    <x-gui::dropdown.separator />
    <x-gui::dropdown.item color="danger" x-on:click="GiraffeUI.toast('Deleted', { color: 'danger' })">Delete</x-gui::dropdown.item>
</x-gui::dropdown>
```

## Custom trigger and position

```blade
<x-gui::dropdown id="account-menu" position="bottom-end">
    <x-slot:trigger>
        <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
            <x-gui::avatar name="Ada Lovelace" alt="Account" />
        </button>
    </x-slot:trigger>

    <x-gui::dropdown.item href="#">Profile</x-gui::dropdown.item>
    <x-gui::dropdown.item href="#">Billing</x-gui::dropdown.item>
    <x-gui::dropdown.separator />
    <x-gui::dropdown.item>Sign out</x-gui::dropdown.item>
</x-gui::dropdown>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `id` | `string` | `required` | Used by the trigger to open it. |
| `label` | `string` | `null` | Text for the default trigger button. |
| `position` | `'bottom-start' \| 'bottom' \| 'bottom-end' \| 'top-start' \| 'top' \| 'top-end' \| 'left' \| 'right'` | `'bottom-start'` | Flips automatically when there is no room. |
| `href (item)` | `string` | `null` | Renders the item as a link. |
| `color (item)` | `'danger' \| null` | `null` | For destructive actions. |
| `disabled (item)` | `bool` | `false` | Shown but not actionable. |
| `name, value, checked (checkbox, radio)` | `string` | `null` | As on any checkbox or radio; wire:model and x-model work too. Attributes go on the input. |
| `heading (group)` | `string` | `null` | A heading that also names the group for screen readers. |
| `label (submenu)` | `string` | `required` | The submenu item text, and the submenu's accessible name. |
| `disabled (submenu)` | `bool` | `false` | Shown but not openable. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `trigger` | A custom trigger. Give it commandfor="{id}" command="toggle-popover". |
| `default` | Items and separators. |
| `leading (item, checkbox, radio, submenu)` | An icon before the label. |
| `default (submenu)` | The submenu's items, checkboxes, radios or further submenus. |
