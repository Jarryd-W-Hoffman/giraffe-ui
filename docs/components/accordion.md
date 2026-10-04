# Accordion

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Sections that expand and collapse. Built on native &lt;details&gt;, so it works without JavaScript.

```blade
<x-gui::accordion exclusive>
    <x-gui::accordion.item title="Question">Answer</x-gui::accordion.item>
</x-gui::accordion>
```

## FAQ

With exclusive, opening one item closes the others.

```blade
<x-gui::accordion exclusive class="mx-auto max-w-lg">
    <x-gui::accordion.item title="Is GiraffeUI free?" open>
        Yes. Every component is MIT licensed.
    </x-gui::accordion.item>
    <x-gui::accordion.item title="Do I need Livewire?">
        No. Components are plain Blade. Livewire works great with them, but it's optional.
    </x-gui::accordion.item>
    <x-gui::accordion.item title="Can I change the colours?">
        Yes. Override the CSS variables in your own theme. See the theming guide.
    </x-gui::accordion.item>
</x-gui::accordion>
```

## Independent items

Without exclusive, any number can be open.

```blade
<x-gui::accordion class="mx-auto max-w-lg">
    <x-gui::accordion.item title="Shipping" open>Orders ship within 2 business days.</x-gui::accordion.item>
    <x-gui::accordion.item title="Returns" open>Return anything within 30 days.</x-gui::accordion.item>
    <x-gui::accordion.item title="Warranty">Two years on all hardware.</x-gui::accordion.item>
</x-gui::accordion>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `exclusive` | `bool` | `false` | Only one item open at a time. |
| `name` | `string` | `generated` | The shared details name when exclusive. |
| `title (item)` | `string` | `required` | The item heading. |
| `open (item)` | `bool` | `false` | Start expanded. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | Accordion items, or an item's content. |
