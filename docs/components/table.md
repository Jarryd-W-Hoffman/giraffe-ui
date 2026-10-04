# Table

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Rows of data, from an array or composed cell by cell, with sorting, an empty state and pagination.

```blade
<x-gui::table :columns="['name', 'email']" :rows="$users" />
```

## From data

Pass a list of keys and they become headings automatically. Keys are read with data_get, so team.name works.

```blade
<x-gui::table :columns="['name' => 'Full name', 'email' => 'Email', 'team.name' => 'Team']" :rows="$users" />
```

## Sorting

Sortable headings link to ?sort=…&direction=… and set aria-sort. Sort the rows yourself (here, the catalog does it like a controller would). Try it.

```blade
<x-gui::table
    :columns="['name', 'email', 'role']"
    :rows="$sortedUsers"
    :sortable="['name', 'email', 'role']"
    :sort="$sort"
    :direction="$direction"
/>
```

## Custom cells

Compose the table for full control over each cell. With Livewire, put wire:click on a heading to sort with an action instead of a link.

```blade
<x-gui::table>
    <x-slot:head>
        <x-gui::table.heading>Member</x-gui::table.heading>
        <x-gui::table.heading>Role</x-gui::table.heading>
        <x-gui::table.heading><span class="sr-only">Actions</span></x-gui::table.heading>
    </x-slot:head>

    @foreach ($users as $user)
        <x-gui::table.row>
            <x-gui::table.cell>
                <div class="flex items-center gap-3">
                    <x-gui::avatar :name="$user['name']" size="sm" />
                    <div>
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user['name'] }}</p>
                        <p class="text-zinc-500 dark:text-zinc-400">{{ $user['email'] }}</p>
                    </div>
                </div>
            </x-gui::table.cell>
            <x-gui::table.cell>
                <x-gui::badge :color="$user['role'] === 'Admin' ? 'primary' : 'secondary'">{{ $user['role'] }}</x-gui::badge>
            </x-gui::table.cell>
            <x-gui::table.cell class="text-right">
                <x-gui::button variant="ghost" size="sm">Edit</x-gui::button>
            </x-gui::table.cell>
        </x-gui::table.row>
    @endforeach
</x-gui::table>
```

## Paginated

Pass a paginator as rows and the table adds pagination for you.

```blade
<x-gui::table :columns="['id' => '#', 'name', 'email', 'role']" :rows="$paginated" />
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
| `columns` | `array` | `null` | A list of keys (['name', 'email']) or a key =&gt; label map. Leave it out to compose the table. |
| `rows` | `iterable` | `[]` | Arrays, objects, models, a collection or a paginator. |
| `sortable` | `array` | `[]` | Column keys whose headings sort. |
| `sort` | `string` | `null` | The column currently sorted. |
| `direction` | `'asc' \| 'desc'` | `'asc'` | The current sort direction. |
| `sortable (heading)` | `string` | `null` | Makes a composed heading sortable by this key. |
| `wrap (cell)` | `bool` | `false` | Let a cell's text wrap. |

On a sortable heading, wire:click and x-on:click go on the sort button; class and other attributes go on the &lt;th&gt;.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `head` | Composed headings (table.heading). |
| `default` | Composed rows (table.row with table.cell). |
| `empty` | Shown when there are no rows. Defaults to "No results." |
| `footer` | Below the table. Defaults to pagination when rows is a paginator. |
