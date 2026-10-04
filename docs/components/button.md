# Button

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Displays a button, or a link that looks like one.

```blade
<x-gui::button>Save</x-gui::button>
```

## Variants

```blade
<x-gui::button>Solid</x-gui::button>
<x-gui::button variant="outline">Outline</x-gui::button>
<x-gui::button variant="ghost">Ghost</x-gui::button>
```

## Colours

```blade
<x-gui::button color="primary">Primary</x-gui::button>
<x-gui::button color="secondary">Secondary</x-gui::button>
<x-gui::button color="success">Success</x-gui::button>
<x-gui::button color="warning">Warning</x-gui::button>
<x-gui::button color="danger">Danger</x-gui::button>
<x-gui::button color="info">Info</x-gui::button>
```

## Outline colours

```blade
<x-gui::button variant="outline" color="primary">Primary</x-gui::button>
<x-gui::button variant="outline" color="secondary">Secondary</x-gui::button>
<x-gui::button variant="outline" color="success">Success</x-gui::button>
<x-gui::button variant="outline" color="warning">Warning</x-gui::button>
<x-gui::button variant="outline" color="danger">Danger</x-gui::button>
<x-gui::button variant="outline" color="info">Info</x-gui::button>
```

## Sizes

```blade
<x-gui::button size="sm">Small</x-gui::button>
<x-gui::button size="md">Medium</x-gui::button>
<x-gui::button size="lg">Large</x-gui::button>
```

## With icons

Use the leading and trailing slots. Mark decorative icons aria-hidden.

```blade
<x-gui::button>
    <x-slot:leading>
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
    </x-slot:leading>
    New project
</x-gui::button>

<x-gui::button variant="outline">
    Continue
    <x-slot:trailing>
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
    </x-slot:trailing>
</x-gui::button>
```

## Icon only

Give icon-only buttons an aria-label so screen readers can name them.

```blade
<x-gui::button variant="ghost" color="secondary" size="sm" aria-label="Close" class="w-8 px-0">
    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
</x-gui::button>
```

## As a link

Pass an href to render an &lt;a&gt;. Add external to open in a new tab safely.

```blade
<x-gui::button href="#">Internal link</x-gui::button>
<x-gui::button href="https://laravel.com" external variant="outline">Laravel docs</x-gui::button>
```

## Disabled

Disabled links lose their href and are marked aria-disabled.

```blade
<x-gui::button disabled>Disabled button</x-gui::button>
<x-gui::button href="#" disabled variant="outline">Disabled link</x-gui::button>
```

## Loading

With Livewire this is automatic: a button with wire:click, or a submit button in a wire:submit form, shows a spinner and ignores clicks while its request runs. Set loading yourself otherwise.

```blade
<x-gui::button loading>Saving</x-gui::button>
<x-gui::button loading variant="outline">
    <x-slot:leading>
        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
    </x-slot:leading>
    Creating
</x-gui::button>
```

## Full width

```blade
<x-gui::button class="w-full">Continue</x-gui::button>
```

## With Alpine

Any attribute passes straight through, including x-on and wire:click.

```blade
<div x-data="{ count: 0 }">
    <x-gui::button x-on:click="count++" variant="outline">
        Clicked <span x-text="count">0</span> times
    </x-gui::button>
</div>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `variant` | `'solid' \| 'outline' \| 'ghost'` | `'solid'` | Visual style. |
| `color` | `'primary' \| 'secondary' \| 'success' \| 'warning' \| 'danger' \| 'info'` | `'primary'` | Theme colour. Unknown values fall back to primary. |
| `size` | `'sm' \| 'md' \| 'lg'` | `'md'` | Height, padding and text size. |
| `href` | `string` | `null` | Renders an &lt;a&gt; instead of a &lt;button&gt;. |
| `external` | `bool` | `false` | With href, opens in a new tab with rel="noopener noreferrer". |
| `type` | `'button' \| 'submit' \| 'reset'` | `'button'` | Ignored for links. |
| `disabled` | `bool` | `false` | Disables a button, or removes the href from a link and sets aria-disabled. |
| `loading` | `bool` | `false` | Shows a spinner in place of the leading icon and disables the button. Automatic for Livewire actions. |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element.

## Slots

| Slot | Description |
|---|---|
| `default` | The label. |
| `leading` | Content before the label, usually an icon. |
| `trailing` | Content after the label. |
