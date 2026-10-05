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

## Selecting rows

table.select puts a real checkbox in a row, and table.select-all in the header ticks or clears them all, showing a mixed state when only some are ticked. Selected rows are highlighted with CSS. In a form, the ticked values submit as selected[]; with Livewire, put wire:model on each table.select.

```blade
<form x-on:submit.prevent="GiraffeUI.toast(new FormData($el).getAll('selected[]').length + ' selected', { color: 'success' })" class="space-y-3">
    <x-gui::table label="Team members">
        <x-slot:head>
            <x-gui::table.select-all label="Select all members" />
            <x-gui::table.heading>Name</x-gui::table.heading>
            <x-gui::table.heading>Role</x-gui::table.heading>
        </x-slot:head>

        @foreach ($users as $user)
            <x-gui::table.row>
                <x-gui::table.select name="selected[]" :value="$user['email']" :label="'Select '.$user['name']" />
                <x-gui::table.cell>{{ $user['name'] }}</x-gui::table.cell>
                <x-gui::table.cell>{{ $user['role'] }}</x-gui::table.cell>
            </x-gui::table.row>
        @endforeach
    </x-gui::table>

    <x-gui::button type="submit" size="sm" variant="outline" color="secondary">Export selected</x-gui::button>
</form>
```

## Sticky header

With max-height, the table scrolls inside that height and the header stays at the top. The scroller is keyboard focusable while it overflows.

```blade
<x-gui::table label="Orders" max-height="16rem">
    <x-slot:head>
        <x-gui::table.heading>Order</x-gui::table.heading>
        <x-gui::table.heading>Status</x-gui::table.heading>
        <x-gui::table.heading align="right">Total</x-gui::table.heading>
    </x-slot:head>

    @foreach (range(1, 20) as $i)
        <x-gui::table.row>
            <x-gui::table.cell>#{{ 1000 + $i }}</x-gui::table.cell>
            <x-gui::table.cell>{{ $i % 3 ? 'Paid' : 'Pending' }}</x-gui::table.cell>
            <x-gui::table.cell align="right" class="tabular-nums">${{ number_format($i * 37.5, 2) }}</x-gui::table.cell>
        </x-gui::table.row>
    @endforeach
</x-gui::table>
```

## Aligning columns

Give a table built from data an align map, or set align on a composed table.heading and table.cell. Right-align numbers so their digits line up.

```blade
<x-gui::table
    :columns="['plan' => 'Plan', 'seats' => 'Seats', 'price' => 'Price']"
    :rows="[['plan' => 'Starter', 'seats' => 1, 'price' => '$29'], ['plan' => 'Pro', 'seats' => 10, 'price' => '$99'], ['plan' => 'Enterprise', 'seats' => 250, 'price' => '$1,499']]"
    :align="['seats' => 'center', 'price' => 'right']"
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
| `columns` | `array` | `null` | A list of keys (['name', 'email']) or a key =&gt; label map. Leave it out to compose the table. |
| `rows` | `iterable` | `[]` | Arrays, objects, models, a collection or a paginator. |
| `sortable` | `array` | `[]` | Column keys whose headings sort. |
| `sort` | `string` | `null` | The column currently sorted. |
| `direction` | `'asc' \| 'desc'` | `'asc'` | The current sort direction. |
| `label` | `string` | `null` | Names the table's scrolling region for screen readers. While a wide table overflows, its scroller is keyboard focusable either way. |
| `align` | `array` | `[]` | Column key =&gt; 'left' \| 'center' \| 'right', for tables built from columns. |
| `max-height` | `string` | `null` | A CSS length, e.g. 20rem. The table scrolls within it under a sticky header. |
| `sortable (heading)` | `string` | `null` | Makes a composed heading sortable by this key. |
| `wrap (cell)` | `bool` | `false` | Let a cell's text wrap. |
| `align (heading, cell)` | `'left' \| 'center' \| 'right'` | `null` | Text alignment, without fighting the built-in classes. |
| `label (select)` | `string` | `'Select row'` | The row checkbox's accessible name, e.g. "Select Ada Lovelace". Other attributes (name, value, checked, wire:model) go on the checkbox. |
| `label (select-all)` | `string` | `'Select all rows'` | The header checkbox's accessible name. |

On a sortable heading, wire:click and x-on:click go on the sort button; class and other attributes go on the &lt;th&gt;.

table.select-all only counts the table.select checkboxes on the current page. It follows rows that change (pagination, Livewire updates) and checkboxes ticked by wire:model, x-model or scripts. Ticking it fires input and change on each row checkbox, so wire:model and x-model keep up; Livewire sends them in one request.

A sticky header can only stick inside the table's own scroller, because the scroller that lets wide tables scroll sideways also contains vertical scrolling. That's why it comes with max-height.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `head` | Composed headings (table.heading). |
| `default` | Composed rows (table.row with table.cell). |
| `empty` | Shown when there are no rows. Defaults to "No results." |
| `footer` | Below the table. Defaults to pagination when rows is a paginator. |
