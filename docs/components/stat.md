# Stat

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A headline number with a label and an optional change.

```blade
<x-gui::stat label="Revenue" value="$48,200" change="12%" trend="up" description="vs last month" />
```

## A row of stats

The arrow and hidden text ("Increased by", "Decreased by") carry the direction, so it never relies on colour. Use invert when down is good, like response time.

```blade
<div class="grid gap-4 sm:grid-cols-3">
    <x-gui::card>
        <x-gui::stat label="Revenue" value="$48,200" change="12%" trend="up" description="vs last month" />
    </x-gui::card>
    <x-gui::card>
        <x-gui::stat label="Churn" value="2.1%" change="0.4 pts" trend="up" invert description="vs last month" />
    </x-gui::card>
    <x-gui::card>
        <x-gui::stat label="Response time" value="182 ms" change="24 ms" trend="down" invert />
    </x-gui::card>
</div>
```

## Display

variant="display" is a big number with its label underneath, centred, for a landing page's "in numbers" strip.

```blade
<div class="grid w-full grid-cols-2 gap-8 lg:grid-cols-4">
    <x-gui::stat variant="display" value="61" label="components" />
    <x-gui::stat variant="display" value="167" label="icons, bundled" />
    <x-gui::stat variant="display" value="3" label="browsers tested" />
    <x-gui::stat variant="display" value="0" label="required build steps" />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'default' \| 'display'` | `'default'` | display: a big centred number with its label under it. |
| `label` | `string` | `` | What the number is. |
| `value` | `string` | `` | The number, formatted. |
| `change` | `string` | `null` | The change, formatted, e.g. 12%. |
| `trend` | `'up' \| 'down' \| null` | `null` | Direction of the change. Adds an arrow and hidden text. |
| `invert` | `bool` | `false` | Down is good: colours a fall green and a rise red. |
| `description` | `string` | `null` | Context after the change, e.g. vs last month. |
| `increase-label` | `string` | `'Increased by'` | Hidden text for up, for translation. |
| `decrease-label` | `string` | `'Decreased by'` | Hidden text for down. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
