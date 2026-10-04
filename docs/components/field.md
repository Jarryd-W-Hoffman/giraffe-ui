# Field

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Wraps a control with a label, hint and error message, and links them for screen readers.

```blade
<x-gui::field label="Email" name="email" hint="We'll never share it.">
    <x-gui::input type="email" />
</x-gui::field>
```

## Label and hint

The control gets its id and name from the field, and aria-describedby points at the hint.

```blade
<x-gui::field label="Email" name="email" hint="We'll never share it.">
    <x-gui::input type="email" placeholder="you@example.com" />
</x-gui::field>
```

## Validation errors

Errors come from Laravel's $errors bag using the field name, or pass one with error. The control is marked aria-invalid.

```blade
<x-gui::field label="Username" name="username" error="That username is taken.">
    <x-gui::input value="ada" />
</x-gui::field>
```

## Required

```blade
<x-gui::field label="Full name" name="name" required>
    <x-gui::input />
</x-gui::field>
```

## With Livewire

Put wire:model on the control and name on the field so errors are found.

```blade
<x-gui::field label="Bio" name="bio">
    <x-gui::textarea wire:model="bio" />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `label` | `string` | `null` | Label text, linked to the control. |
| `name` | `string` | `null` | Passed to the control and used to find errors. Array names like address[city] work. |
| `id` | `string` | `gui-{name}` | Id for the control. Derived from name by default. |
| `hint` | `string` | `null` | Help text shown under the control. |
| `error` | `string` | `null` | An error message. Defaults to the first error for name. |
| `required` | `bool` | `false` | Marks the label and makes the control required. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The control: input, textarea or select. |
