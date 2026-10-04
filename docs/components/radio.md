# Radio

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A native radio button. Group them in a fieldset.

```blade
<x-gui::radio name="plan" value="pro" label="Pro" />
```

## Radio group

```blade
<x-gui::fieldset legend="Plan" hint="You can change this later." name="plan">
    <x-gui::radio name="plan" value="free" label="Free" description="For side projects." />
    <x-gui::radio name="plan" value="pro" label="Pro" description="For growing teams." checked />
    <x-gui::radio name="plan" value="enterprise" label="Enterprise" description="Talk to us." disabled />
</x-gui::fieldset>
```

## Group error

```blade
<x-gui::fieldset legend="Delivery" name="delivery" error="Choose a delivery option.">
    <x-gui::radio name="delivery" value="standard" label="Standard" :invalid="true" />
    <x-gui::radio name="delivery" value="express" label="Express" :invalid="true" />
</x-gui::fieldset>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | Label text. Or use the slot. |
| `description` | `string` | `null` | Help text, linked with aria-describedby. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label, when it needs markup. |
