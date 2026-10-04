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

## Grows with its content

Uses CSS field-sizing, so no JavaScript. rows is the minimum height and max-rows caps it, after which it scrolls. Browsers without field-sizing keep the fixed height and the resize handle.

```blade
<x-gui::field label="Message" name="message">
    <x-gui::textarea rows="2" autosize :max-rows="8" placeholder="Start typing and keep going…" />
</x-gui::field>
```

## Character count

With maxlength the limit is part of the accessible description. The visible count is hidden from screen readers, and a polite live region says how many characters are left once typing pauses.

```blade
<x-gui::field label="About you" name="about" hint="Shown on your profile.">
    <x-gui::textarea counter maxlength="160" autosize>I build things.</x-gui::textarea>
</x-gui::field>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `rows` | `int` | `3` | Visible lines of text. With autosize, the minimum. |
| `autosize` | `bool` | `false` | Grow with the content, using CSS field-sizing. |
| `max-rows` | `int \| null` | `null` | With autosize, the most lines before it scrolls. |
| `counter` | `bool` | `false` | Show a character count, against maxlength when there is one. Uses Alpine. |
| `invalid` | `bool \| null` | `null` | Force the invalid state. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | The initial text. Defaults to old input. |
