# Code block

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A block of code with a Copy button. It scrolls sideways rather than wrapping, and takes keyboard focus to do so.

```blade
<x-gui::code-block :code="$snippet" />
```

## Basic

Pass the code as code (or in the slot, for plain text). The button says Copied for a moment, and screen readers hear it.

```blade
<x-gui::code-block class="w-full" code="php artisan vendor:publish --tag=giraffeui-lang" />
```

## Live

live takes an Alpine expression: the block shows, and copies, its value as it changes.

```blade
<div x-data="{ color: 'violet' }" class="w-full space-y-3">
    <x-gui::segmented-control name="code_block_color" label="Colour" size="sm" x-model="color" :options="['violet' => 'Violet', 'emerald' => 'Emerald', 'rose' => 'Rose']" value="violet" />
    <x-gui::code-block live="`@theme {\n    --color-gui-primary: var(--color-${color}-600);\n}`" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `code` | `string` | `null` | The code. Or put plain text in the slot. |
| `live` | `string` | `null` | An Alpine expression to show instead, updated as it changes. |
| `copy-label, copied-label` | `string` | `'Copy', 'Copied'` | The button's text (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
