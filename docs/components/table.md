# Table

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Displays rows of data with headings and an empty state.

```blade
<x-gui::table :columns="['name', 'email']" :rows="$users" />
```

## Basic

Pass a list of keys and they become headings automatically.

```blade
<x-gui::table :columns="['name', 'email', 'role']" :rows="$users" />
```

## Custom labels and nested keys

Use a key => label map. Keys are read with data_get, so team.name works.

```blade
<x-gui::table
    :columns="['name' => 'Full name', 'team.name' => 'Team', 'role' => 'Role']"
    :rows="$users"
/>
```

## Empty state

```blade
<x-gui::table :columns="['name', 'email']" :rows="[]">
    <x-slot:empty>No users yet. Invite your first teammate.</x-slot:empty>
</x-gui::table>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `columns` | `array` | `[]` | A list of keys (['name', 'email']) or a key => label map. |
| `rows` | `iterable` | `[]` | Arrays, objects, Eloquent models or a collection. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `empty` | Shown when there are no rows. Defaults to "No results." |
