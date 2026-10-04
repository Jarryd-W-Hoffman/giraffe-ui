# Dropdown

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A menu of actions. A native popover positioned with CSS anchor positioning, with the menu keyboard pattern added by Alpine.

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

## Custom trigger and position

```blade
<x-gui::dropdown id="account-menu" position="bottom-end">
    <x-slot:trigger>
        <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Account" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
            <x-gui::avatar name="Ada Lovelace" />
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

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `trigger` | A custom trigger. Give it commandfor="{id}" command="toggle-popover". |
| `default` | Items and separators. |
| `leading (item)` | An icon before the item label. |
