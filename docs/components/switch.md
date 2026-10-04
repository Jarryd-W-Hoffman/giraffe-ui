# Switch

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

An on/off toggle. A real checkbox with role="switch" underneath.

```blade
<x-gui::switch name="notifications" label="Email notifications" />
```

## With a description

```blade
<x-gui::switch name="digest" label="Weekly digest" description="A summary of activity every Monday." checked />
```

## Settings list

```blade
<x-gui::fieldset legend="Privacy">
    <x-gui::switch name="public" label="Public profile" checked />
    <x-gui::switch name="search" label="Show me in search results" />
    <x-gui::switch name="analytics" label="Share usage data" disabled />
</x-gui::fieldset>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | Label text. Or use the slot. |
| `description` | `string` | `null` | Help text, linked with aria-describedby. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label, when it needs markup. |
