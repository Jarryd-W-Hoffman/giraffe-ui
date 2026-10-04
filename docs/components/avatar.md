# Avatar

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A profile image, with initials when there is no image.

```blade
<x-gui::avatar :src="$user->photo_url" :name="$user->name" />
```

## Image

```blade
<x-gui::avatar :src="$photo" name="Ada Lovelace" size="lg" />
```

## Initials and fallback

```blade
<x-gui::avatar name="Grace Hopper" size="sm" />
<x-gui::avatar name="Alan Turing" />
<x-gui::avatar name="Émile Ångström" size="lg" />
<x-gui::avatar />
```

## Stacked

Use Avatar group to overlap avatars and count the rest.

```blade
<x-gui::avatar.group :avatars="$users" :max="3" label="Team" />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `src` | `string` | `null` | Image URL. |
| `name` | `string` | `null` | Used for initials and as the accessible name. |
| `alt` | `string` | `null` | Overrides the accessible name. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` |  |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
