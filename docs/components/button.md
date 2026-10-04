# Button

```blade
<x-gui::button>Save</x-gui::button>
```

Renders a `<button>`, or an `<a>` when given an `href`.

## Props

| Prop | Type | Default | Notes |
|---|---|---|---|
| `variant` | `solid` \| `outline` \| `ghost` | `solid` | |
| `color` | `primary` \| `secondary` \| `success` \| `warning` \| `danger` \| `info` | `primary` | Unknown values fall back to `primary`. |
| `size` | `sm` \| `md` \| `lg` | `md` | |
| `href` | string | `null` | Renders a link instead of a button. |
| `external` | bool | `false` | With `href`, opens in a new tab with `rel="noopener noreferrer"`. |
| `type` | `button` \| `submit` \| `reset` | `button` | Ignored for links. |
| `disabled` | bool | `false` | Buttons get the `disabled` attribute. Links lose their `href` and get `aria-disabled="true"`. |

Any other attribute (`class`, `wire:click`, `x-on:click`, `wire:navigate`, `data-*`, …) is passed through to the element.

## Slots

| Slot | Purpose |
|---|---|
| default | The label. |
| `leading` | Content before the label, usually an icon. |
| `trailing` | Content after the label. |

## Examples

```blade
{{-- Submit a form --}}
<x-gui::button type="submit">Create account</x-gui::button>

{{-- Livewire action --}}
<x-gui::button color="danger" variant="outline" wire:click="delete">Delete</x-gui::button>

{{-- Full width --}}
<x-gui::button class="w-full">Continue</x-gui::button>

{{-- Link with an icon, using Livewire's SPA navigation --}}
<x-gui::button href="{{ route('settings') }}" variant="ghost" wire:navigate>
    <x-slot:leading>
        <svg class="size-4" aria-hidden="true">…</svg>
    </x-slot:leading>
    Settings
</x-gui::button>

{{-- External link --}}
<x-gui::button href="https://laravel.com" external size="sm">Laravel docs</x-gui::button>
```

## Accessibility

- Buttons default to `type="button"` so they never submit a form by accident.
- Focus is shown with a visible outline (`focus-visible`).
- Disabled links are removed from navigation and announced as disabled.
- Icon-only buttons need an accessible name: `<x-gui::button aria-label="Close">…</x-gui::button>`.
