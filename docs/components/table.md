# Table

```blade
<x-gui::table :columns="['name', 'email']" :rows="$users" />
```

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `columns` | array | `[]` | A list of keys (`['name', 'email']`), labelled automatically ("First Name" from `first_name`), or a `key => label` map. |
| `rows` | iterable | `[]` | Arrays, objects, Eloquent models or a collection. |

Keys are read with `data_get`, so nested values work: `['team.name' => 'Team']`.
Every value is escaped.

Extra attributes such as `class` go on the scrolling wrapper.

## Slots

| Slot | Purpose |
|---|---|
| `empty` | Shown when there are no rows. Defaults to "No results." |

## Examples

```blade
<x-gui::table
    :columns="['name' => 'Name', 'email' => 'Email address', 'team.name' => 'Team']"
    :rows="$users"
    class="mt-6"
>
    <x-slot:empty>No users yet. Invite your first teammate.</x-slot:empty>
</x-gui::table>
```

## Coming later

Custom cell content, sortable headers and pagination are planned for v0.3.
