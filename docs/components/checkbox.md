# Checkbox

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native checkbox with a label and optional description.

```blade
<x-gui::checkbox name="remember" label="Remember me" />
```

## With a description

```blade
<x-gui::checkbox name="marketing" label="Product updates" description="A short email when we ship something new." checked />
```

## Rich label

Use the slot when the label needs markup.

```blade
<x-gui::checkbox name="terms">
    I agree to the <a href="#" class="underline">terms of service</a>
</x-gui::checkbox>
```

## Group

Wrap related checkboxes in a fieldset. Each gets its own id from its name and value.

```blade
<x-gui::fieldset legend="Notify me about" name="notify">
    <x-gui::checkbox name="notify[]" value="comments" label="Comments" checked />
    <x-gui::checkbox name="notify[]" value="mentions" label="Mentions" checked />
    <x-gui::checkbox name="notify[]" value="follows" label="New followers" />
</x-gui::fieldset>
```

## Disabled

```blade
<x-gui::checkbox name="locked" label="Managed by your admin" checked disabled />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | Label text. Or use the slot. |
| `description` | `string` | `null` | Help text, linked with aria-describedby. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. By default it follows the error bag for name. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label, when it needs markup. |
