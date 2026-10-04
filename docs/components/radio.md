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

## Cards

The whole card is the label, so clicking anywhere on it picks the option. They are still native radios: Tab reaches the group and arrow keys move through it.

```blade
<x-gui::fieldset legend="Server size" hint="You can resize later." name="size">
    <div class="grid gap-3 sm:grid-cols-3">
        <x-gui::radio variant="card" name="size" value="small" label="Small" description="1 CPU, 2 GB" />
        <x-gui::radio variant="card" name="size" value="medium" label="Medium" description="2 CPUs, 4 GB" checked />
        <x-gui::radio variant="card" name="size" value="large" label="Large" description="Sold out" disabled />
    </div>
</x-gui::fieldset>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | Label text. Or use the slot. |
| `description` | `string` | `null` | Help text, linked with aria-describedby. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |
| `variant` | `'default' \| 'card'` | `'default'` | card makes the whole bordered card the label. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The label, when it needs markup. |
