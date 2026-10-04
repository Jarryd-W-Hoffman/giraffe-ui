# Tabs

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Switch between panels of related content. Follows the WAI-ARIA tabs pattern. Needs Alpine, which Livewire includes.

```blade
<x-gui::tabs :tabs="['account' => 'Account', 'password' => 'Password']">
    <x-gui::tabs.panel name="account">…</x-gui::tabs.panel>
    <x-gui::tabs.panel name="password">…</x-gui::tabs.panel>
</x-gui::tabs>
```

## Underline

Arrow keys move between tabs, Home and End jump to the first and last, and Tab moves into the panel.

```blade
<x-gui::tabs :tabs="['account' => 'Account', 'password' => 'Password', 'notifications' => 'Notifications']" class="mx-auto max-w-lg">
    <x-gui::tabs.panel name="account">
        <x-gui::field label="Name" name="name"><x-gui::input value="Ada Lovelace" /></x-gui::field>
    </x-gui::tabs.panel>
    <x-gui::tabs.panel name="password">
        <x-gui::field label="New password" name="password"><x-gui::input type="password" /></x-gui::field>
    </x-gui::tabs.panel>
    <x-gui::tabs.panel name="notifications">
        <x-gui::switch name="digest" label="Weekly digest" checked />
    </x-gui::tabs.panel>
</x-gui::tabs>
```

## Pills

```blade
<x-gui::tabs variant="pills" :tabs="['monthly' => 'Monthly', 'yearly' => 'Yearly']" selected="yearly" class="mx-auto max-w-lg">
    <x-gui::tabs.panel name="monthly"><p class="text-2xl font-semibold">$12 <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">/ month</span></p></x-gui::tabs.panel>
    <x-gui::tabs.panel name="yearly"><p class="text-2xl font-semibold">$120 <span class="text-sm font-normal text-zinc-600 dark:text-zinc-400">/ year</span></p></x-gui::tabs.panel>
</x-gui::tabs>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `tabs` | `array` | `[]` | name =&gt; label pairs, in order. |
| `selected` | `string` | `first tab` | The tab selected on load. |
| `variant` | `'underline' \| 'pills'` | `'underline'` |  |
| `id` | `string` | `generated` | Prefix for tab and panel ids. |
| `name (panel)` | `string` | `required` | Matches a key in tabs. |

Bind the selected tab with wire:model="tab" or x-model.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The panels. |
