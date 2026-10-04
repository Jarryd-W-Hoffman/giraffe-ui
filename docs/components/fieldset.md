# Fieldset

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Groups related controls under a legend, with a shared hint and error.

```blade
<x-gui::fieldset legend="Plan" name="plan">
    …
</x-gui::fieldset>
```

## Address

```blade
<x-gui::fieldset legend="Shipping address" hint="We only ship within Australia.">
    <x-gui::field label="Street" name="address[street]">
        <x-gui::input autocomplete="street-address" />
    </x-gui::field>
    <div class="grid grid-cols-2 gap-3">
        <x-gui::field label="City" name="address[city]">
            <x-gui::input autocomplete="address-level2" />
        </x-gui::field>
        <x-gui::field label="Postcode" name="address[postcode]">
            <x-gui::input autocomplete="postal-code" inputmode="numeric" />
        </x-gui::field>
    </div>
</x-gui::fieldset>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `legend` | `string` | `null` | The group's label. |
| `hint` | `string` | `null` | Help text for the whole group. |
| `name` | `string` | `null` | Used to find a group error, e.g. "roles" for roles[] checkboxes. |
| `error` | `string` | `null` | An error message. Defaults to the first error for name. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The grouped controls. |
