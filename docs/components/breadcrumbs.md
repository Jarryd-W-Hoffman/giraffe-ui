# Breadcrumbs

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Shows where the current page sits in the site. Follows the WAI-ARIA breadcrumb pattern.

```blade
<x-gui::breadcrumbs :items="['Home' => '/', 'Projects' => '/projects', 'GiraffeUI' => null]" />
```

## From an array

Pass label =&gt; url pairs. The item with a null url is the current page and gets aria-current="page".

```blade
<x-gui::breadcrumbs :items="['Home' => '#', 'Projects' => '#', 'GiraffeUI' => null]" />
```

## Composed, with an icon

```blade
<x-gui::breadcrumbs>
    <x-gui::breadcrumbs.item href="#">
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" /></svg>
        <span class="sr-only">Home</span>
    </x-gui::breadcrumbs.item>
    <x-gui::breadcrumbs.item href="#">Settings</x-gui::breadcrumbs.item>
    <x-gui::breadcrumbs.item>Billing</x-gui::breadcrumbs.item>
</x-gui::breadcrumbs>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `items` | `array` | `null` | label =&gt; url pairs. Use null for the current page. Or compose breadcrumbs.item. |
| `href (item)` | `string` | `null` | Leave it out on the current page. |

Separators are decorative and hidden from screen readers; the list order already conveys the hierarchy.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | breadcrumbs.item elements, when not using items. |
