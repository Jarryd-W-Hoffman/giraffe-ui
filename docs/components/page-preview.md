# Page preview

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A link to a page, with the live page shown small above its name. For galleries of examples or templates.

```blade
<x-gui::page-preview href="/dashboard" title="Dashboard" description="Charts and stats." />
```

## Basic

The page loads lazily in an inert frame at a third of its size, so it's a picture you can't tab into. src defaults to href.

```blade
<div class="grid w-full gap-4 md:grid-cols-2">
    <x-gui::page-preview href="/catalog/examples/projects" title="Projects" description="A board of tasks in a workspace." />
    <x-gui::page-preview href="/catalog/examples/pricing" title="Pricing" description="Plans, a billing switch and FAQs." />
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `href` | `string` | `` | Where it links. |
| `src` | `string` | `href` | The page shown, if not the link. |
| `title, description` | `string` | `` | The name and a line under it. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.
