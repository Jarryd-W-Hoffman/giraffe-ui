# Description list

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Terms and their details, like the fields of a record. A real dl, in two columns from the sm breakpoint.

```blade
<x-gui::description-list :items="['Name' => $user->name, 'Email' => $user->email]" />
```

## From an array

```blade
<x-gui::description-list :items="['Full name' => 'Ada Lovelace', 'Role' => 'Analyst', 'Email' => 'ada@example.com']" />
```

## Composed

Use items when a detail needs markup.

```blade
<x-gui::card title="Order #1042" description="Placed on 5 October 2026.">
    <x-gui::description-list>
        <x-gui::description-list.item term="Status">
            <x-gui::badge color="success">Paid</x-gui::badge>
        </x-gui::description-list.item>
        <x-gui::description-list.item term="Customer">
            <div class="flex items-center gap-2">
                <x-gui::avatar name="Grace Hopper" size="sm" />
                Grace Hopper
            </div>
        </x-gui::description-list.item>
        <x-gui::description-list.item term="Total">$248.00</x-gui::description-list.item>
    </x-gui::description-list>
</x-gui::card>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `items` | `array` | `[]` | term =&gt; detail pairs. Details are escaped. |

description-list.item takes a term prop, and its slot is the detail.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | description-list.item components, after any items. |
