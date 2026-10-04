# Icon

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Heroicons that GiraffeUI ships with, or any icon from a blade-icons set.

```blade
<x-gui::icon name="check" />
```

## Bundled icons

These come with GiraffeUI and are what its components use.

```blade
<div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
    @foreach (array_keys(\JayAitch\GiraffeUI\Support\Icons::PATHS) as $name)
        <div class="flex flex-col items-center gap-2 rounded-gui p-3 text-center text-xs text-zinc-600 dark:text-zinc-400">
            <x-gui::icon :name="$name" class="text-zinc-900 dark:text-zinc-100" />
            {{ $name }}
        </div>
    @endforeach
</div>
```

## Sizes and colours

```blade
<x-gui::icon name="check-circle" size="xs" class="text-gui-success-text" />
<x-gui::icon name="check-circle" size="sm" class="text-gui-success-text" />
<x-gui::icon name="check-circle" class="text-gui-success-text" />
<x-gui::icon name="check-circle" size="lg" class="text-gui-success-text" />
```

## Meaningful icons

Icons are hidden from screen readers unless you give them a label. Label an icon when it carries meaning on its own.

```blade
<p class="flex items-center gap-2 text-sm">
    <x-gui::icon name="exclamation-triangle" size="sm" label="Warning" class="text-gui-warning-text" />
    Your card expires this month.
</p>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `name` | `string` | `required` | A bundled icon, or any blade-icons name such as heroicon-o-bell. |
| `size` | `'xs' \| 'sm' \| 'md' \| 'lg'` | `'md'` | 14, 16, 20 or 24px. |
| `label` | `string` | `null` | Announces the icon as an image. Without it the icon is aria-hidden. |

For any other icon, install blade-ui-kit/blade-icons and an icon set (e.g. blade-ui-kit/blade-heroicons), then use its names: &lt;x-gui::icon name="heroicon-o-bell" /&gt;.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
