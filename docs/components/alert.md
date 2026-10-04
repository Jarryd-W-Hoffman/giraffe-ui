# Alert

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A message that stands out from the page, with an icon, title and actions.

```blade
<x-gui::alert color="success" title="Saved">Your changes are live.</x-gui::alert>
```

## Colours

```blade
<div class="space-y-3">
    <x-gui::alert title="New version available">Refresh to get the latest features.</x-gui::alert>
    <x-gui::alert color="success" title="Payment received">Thanks! A receipt is on its way.</x-gui::alert>
    <x-gui::alert color="warning" title="Your trial ends in 3 days">Add a card to keep your projects.</x-gui::alert>
    <x-gui::alert color="danger" title="Deploy failed">The build exited with code 1.</x-gui::alert>
</div>
```

## With actions

```blade
<x-gui::alert color="warning" title="Unsaved changes">
    You have edits that haven't been published.
    <x-slot:actions>
        <x-gui::button size="sm" variant="ghost" color="warning">Discard</x-gui::button>
        <x-gui::button size="sm" color="warning">Publish</x-gui::button>
    </x-slot:actions>
</x-gui::alert>
```

## Announced to screen readers

Alerts are static by default. When one appears after the page loads, add role="alert" (urgent) or role="status" (polite).

```blade
<x-gui::alert color="success" role="status">Settings saved.</x-gui::alert>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `color` | `'info' \| 'success' \| 'warning' \| 'danger' \| 'primary' \| 'secondary'` | `'info'` | Colour and default icon. |
| `title` | `string` | `null` | A bold first line. |
| `icon` | `bool` | `true` | Set :icon="false" to hide the icon, or use the icon slot to replace it. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The message. |
| `icon` | Replaces the default icon. |
| `actions` | Buttons or links on the right. |
