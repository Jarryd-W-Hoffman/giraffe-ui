# Textarea

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A multi-line text input.

```blade
<x-gui::textarea name="bio" rows="4" />
```

## In a field

```blade
<x-gui::field label="Bio" name="bio" hint="A sentence or two about you.">
    <x-gui::textarea rows="4" placeholder="I build things…" />
</x-gui::field>
```

## With content

```blade
<x-gui::textarea aria-label="Notes">Remember the milk.</x-gui::textarea>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `rows` | `int` | `3` | Visible lines of text. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The initial text. Defaults to old input. |
