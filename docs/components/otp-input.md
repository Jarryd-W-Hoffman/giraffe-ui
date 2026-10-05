# OTP input

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A one-time code or PIN, shown as one box per character. It is one real input underneath, so phone autofill, pasting and screen readers work as they would on any field.

```blade
<x-gui::otp-input name="code" />
```

## In a field

Type or paste the code. Only digits are kept, up to the length. Backspace, arrow keys and selection work as in any input, and the boxes follow.

```blade
<x-gui::field label="Verification code" name="code" hint="We sent a 6-digit code to ada@example.com.">
    <x-gui::otp-input />
</x-gui::field>
```

## Submits when complete

With autosubmit, entering the last character submits the form, so nobody has to find the button.

```blade
<form x-on:submit.prevent="GiraffeUI.toast('Checking ' + new FormData($el).get('pin'), { color: 'success' })" class="space-y-3">
    <x-gui::field label="PIN" name="pin">
        <x-gui::otp-input :length="4" autosubmit />
    </x-gui::field>
    <x-gui::button type="submit" size="sm">Unlock</x-gui::button>
</form>
```

## Letters and numbers

```blade
<x-gui::field label="Backup code" name="backup_code">
    <x-gui::otp-input :length="8" alphanumeric size="sm" />
</x-gui::field>
```

## With an error

```blade
<x-gui::field label="Code" name="bad_code" error="That code has expired. We've sent a new one.">
    <x-gui::otp-input />
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `length` | `int` | `6` | How many characters. |
| `alphanumeric` | `bool` | `false` | Allow letters too (shown in capitals). Digits only by default. |
| `autosubmit` | `bool` | `false` | Submit the form once every character is entered. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |
| `invalid` | `bool \| null` | `null` | Force the invalid state. By default it follows the error bag. |

name, wire:model, x-model, required and disabled go on the input. The code is never refilled from old input.

Without JavaScript it is a plain text field with the right keyboard, length and pattern.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
