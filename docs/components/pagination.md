# Pagination

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Page links for any Laravel paginator, with a Livewire version.

```blade
<x-gui::pagination :paginator="$users" />
```

## Links

```blade
<x-gui::pagination :paginator="$paginated" />
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `paginator` | `Paginator` | `required` | A length-aware or simple paginator. |
| `livewire` | `bool` | `false` | Use wire:click with Livewire's gotoPage, previousPage and nextPage instead of links. |
| `window` | `int` | `2` | Pages shown either side of the current one. |

Use it for every paginator: Paginator::defaultView('giraffeui::pagination.default') in a service provider.

In Livewire, return 'giraffeui::pagination.livewire' from paginationView(), or pass livewire to the component.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
