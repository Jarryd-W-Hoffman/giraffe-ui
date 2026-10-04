# Empty state

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

What to show when there is nothing to show yet, with a way forward.

```blade
<x-gui::empty-state title="No projects yet" description="Create one to get started." />
```

## With an icon and actions

```blade
<x-gui::empty-state title="No projects yet" description="Projects group your deployments, domains and team. Create one to get started.">
    <x-slot:icon>
        <svg class="size-6" viewBox="0 0 20 20" fill="currentColor"><path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" /></svg>
    </x-slot:icon>
    <x-slot:actions>
        <x-gui::button>New project</x-gui::button>
        <x-gui::button variant="ghost" color="secondary" href="#">Import</x-gui::button>
    </x-slot:actions>
</x-gui::empty-state>
```

## In a table

```blade
<x-gui::table :columns="['name', 'email']" :rows="[]">
    <x-slot:empty>
        <x-gui::empty-state title="No members" description="Invite people to collaborate on this project." class="py-6!">
            <x-slot:actions>
                <x-gui::button size="sm" variant="outline">Invite</x-gui::button>
            </x-slot:actions>
        </x-gui::empty-state>
    </x-slot:empty>
</x-gui::table>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `title` | `string` | `required` | A short heading (an h3). |
| `description` | `string` | `null` | What this is and what to do next. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `icon` | A decorative icon above the title. |
| `actions` | Buttons or links. |
