# Card

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A surface for grouping content, with an optional header and footer.

```blade
<x-gui::card title="Team" description="Manage who has access.">
    …
</x-gui::card>
```

## Header, body and footer

```blade
<x-gui::card title="Create project" description="Deploy a new project in one click." class="mx-auto max-w-md">
    <div class="space-y-4">
        <x-gui::field label="Name" name="project">
            <x-gui::input placeholder="my-app" />
        </x-gui::field>
        <x-gui::field label="Framework" name="framework">
            <x-gui::select :options="['laravel' => 'Laravel', 'statamic' => 'Statamic']" />
        </x-gui::field>
    </div>

    <x-slot:footer>
        <x-gui::button variant="ghost" color="secondary">Cancel</x-gui::button>
        <x-gui::button class="ml-auto">Deploy</x-gui::button>
    </x-slot:footer>
</x-gui::card>
```

## With header actions

```blade
<x-gui::card title="Members" description="4 people" class="mx-auto max-w-md">
    <x-slot:actions>
        <x-gui::button size="sm" variant="outline" color="secondary">Invite</x-gui::button>
    </x-slot:actions>

    <ul class="space-y-3">
        @foreach ($users as $user)
            <li class="flex items-center gap-3">
                <x-gui::avatar :name="$user['name']" size="sm" />
                <span class="text-sm font-medium">{{ $user['name'] }}</span>
                <x-gui::badge class="ml-auto" size="sm">{{ $user['role'] }}</x-gui::badge>
            </li>
        @endforeach
    </ul>
</x-gui::card>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `title` | `string` | `null` | Heading (an h3). |
| `description` | `string` | `null` | Text under the title. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The body. |
| `actions` | Buttons in the header, on the right. |
| `footer` | A footer row with a top border. |
