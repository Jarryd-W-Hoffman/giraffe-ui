# Snippet

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

One line of code to copy, such as an install command: a prompt, the code and a copy button.

```blade
<x-gui::snippet value="composer require jayaitch/giraffeui" />
```

## Basic

The prompt is decoration and isn't copied; :prompt="false" leaves it out.

```blade
<div class="space-y-3">
    <x-gui::snippet value="composer require jayaitch/giraffeui" />
    <x-gui::snippet value="npm install alpinejs" prompt=">" />
    <x-gui::snippet value="php artisan giraffeui:install" :prompt="false" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `value` | `string` | `null` | The code to show and copy. Or put it in the slot. |
| `prompt` | `string \| false` | `'$'` | Shown before the code, not copied. |
| `copy-label` | `string` | `'Copy'` | The copy button's accessible name (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
