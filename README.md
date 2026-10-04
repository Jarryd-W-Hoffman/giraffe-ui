# GiraffeUI

Free, accessible Blade + Alpine components for Laravel and Tailwind CSS v4.
Works great with Livewire, but never requires it.

> **Status:** early development (pre-1.0). Expect breaking changes between minor versions until 1.0.

## Requirements

- PHP 8.4+
- Laravel 13
- Tailwind CSS v4 (new Laravel apps already have it)

## Installation

```bash
composer require jayaitch/giraffeui
php artisan gui:install
```

The installer:

1. adds GiraffeUI's CSS to `resources/css/app.css`, right after `@import "tailwindcss"`;
2. offers to install Livewire (optional, off by default);
3. offers to publish an app layout with dark mode at `resources/views/components/layouts/app.blade.php`. If you already have one, you can install it under another name, overwrite it, or skip.

It is safe to run again. It never runs npm or edits Tailwind config files.

### Manual installation

Add one line to `resources/css/app.css`:

```css
@import "tailwindcss";
@import "../../vendor/jayaitch/giraffeui/resources/css/giraffeui.css";
```

Then rebuild your CSS (`npm run dev` or `npm run build`).

## Usage

```blade
<x-gui::button color="success" wire:click="save">Save</x-gui::button>

<x-gui::table :columns="['name', 'email']" :rows="$users" />
```

## Components

| Group | Components |
|---|---|
| Actions | [Button](docs/components/button.md) |
| Forms | [Field](docs/components/field.md), [Input](docs/components/input.md), [Textarea](docs/components/textarea.md), [Select](docs/components/select.md), [Combobox](docs/components/combobox.md), [Checkbox](docs/components/checkbox.md), [Radio](docs/components/radio.md), [Switch](docs/components/switch.md), [Fieldset](docs/components/fieldset.md), [Label](docs/components/label.md) |
| Display | [Alert](docs/components/alert.md), [Icon](docs/components/icon.md), [Avatar](docs/components/avatar.md), [Badge](docs/components/badge.md), [Card](docs/components/card.md), [Separator](docs/components/separator.md), [Spinner](docs/components/spinner.md), [Progress](docs/components/progress.md), [Kbd](docs/components/kbd.md), [Skeleton](docs/components/skeleton.md), [Empty state](docs/components/empty-state.md) |
| Navigation | [Breadcrumbs](docs/components/breadcrumbs.md) |
| Overlays | [Modal](docs/components/modal.md), [Drawer](docs/components/drawer.md), [Command palette](docs/components/command-palette.md), [Dropdown](docs/components/dropdown.md), [Popover](docs/components/popover.md), [Tooltip](docs/components/tooltip.md), [Toast](docs/components/toasts.md) |
| Disclosure | [Accordion](docs/components/accordion.md), [Tabs](docs/components/tabs.md) |
| Data | [Table](docs/components/table.md), [Pagination](docs/components/pagination.md) |

### Forms

Wrap a control in a field to get a linked label, hint and error message. Errors come from Laravel's `$errors` bag, using the field's `name`:

```blade
<form method="POST" action="/profile">
    @csrf

    <x-gui::field label="Email" name="email" hint="We'll never share it." required>
        <x-gui::input type="email" />
    </x-gui::field>

    <x-gui::button type="submit">Save</x-gui::button>
</form>
```

The input gets `id`, `name`, `required`, `aria-invalid` and `aria-describedby` automatically, and is refilled with old input after a failed submission. With Livewire, put `wire:model` on the control and keep `name` on the field. Buttons that trigger Livewire requests show a loading spinner on their own.

### Interactivity

GiraffeUI uses the platform first. Modal is a native `<dialog>`, Dropdown and Popover are native popovers positioned with CSS anchor positioning, and all three open with `command`/`commandfor` buttons. Accordion is native `<details>`. None of them needs JavaScript to open, close or position. Where the browser has no built-in behaviour (Tabs, menu arrow keys, tooltip Escape, toasts), GiraffeUI uses [Alpine](https://alpinejs.dev). Livewire includes Alpine; in other apps, install Alpine yourself.

```blade
<x-gui::button commandfor="confirm" command="show-modal">Delete</x-gui::button>

<x-gui::modal id="confirm" title="Delete project?">
    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button color="danger">Delete</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
```

## Conventions

Every component follows the same rules:

- **Props:** `variant` (`solid`, `outline`, `ghost`), `color` (`primary`, `secondary`, `success`, `warning`, `danger`, `info`), `size` (`sm`, `md`, `lg`).
- **Attributes pass through.** `class`, `wire:*`, `x-*`, `data-*` and anything else lands on the component's main element.
- **Override built-in classes with `!`.** Your classes are added to the component's own, and when two set the same property, the order in the stylesheet decides which wins, not the order you wrote them. To replace one, use Tailwind's important modifier: `<x-gui::button class="px-0!">`, `<x-gui::skeleton class="rounded-full!">`.
- **Slots, not HTML strings,** for icons and other parts (e.g. `<x-slot:leading>`).
- **Theming is CSS variables only.** There is no PHP config.

## Theming

Override any token in your own `@theme` block, after the GiraffeUI import:

```css
@theme {
    --color-gui-primary: var(--color-violet-600);
    --color-gui-primary-foreground: var(--color-white);
    --radius-gui: var(--radius-lg);
}
```

| Token | Default |
|---|---|
| `--color-gui-primary` | indigo-600 |
| `--color-gui-secondary` | zinc-600 |
| `--color-gui-success` | emerald-700 |
| `--color-gui-warning` | amber-400 |
| `--color-gui-danger` | red-600 |
| `--color-gui-info` | sky-700 |
| `--color-gui-input` | zinc-500 (input borders and switch tracks) |
| `--radius-gui` | `--radius-md` |

Each colour has three tokens:

- `--color-gui-{name}`: solid backgrounds.
- `--color-gui-{name}-foreground`: text on a solid background.
- `--color-gui-{name}-text`: text, borders and focus rings on the page background. Dark mode uses a lighter shade.

Every default pairing meets WCAG AA (4.5:1) in light and dark mode. If you change a colour, set its dark-mode text shade too:

```css
@layer base {
    :root {
        @variant dark {
            --color-gui-primary-text: var(--color-violet-400);
        }
    }
}
```

## Dark mode

Components use Tailwind's `dark:` variant. By default Tailwind follows the OS setting. The published layout switches to class-based dark mode instead. It adds `@custom-variant dark (&:where(.dark, .dark *));` to your CSS and sets the `.dark` class before the page paints, using the saved choice or the OS setting. Switch themes from anywhere with:

```js
GiraffeUI.setTheme('dark'); // or 'light', or 'system'
```

## Development

```bash
composer install
npm install
composer test        # Pest
composer lint        # Pint
composer catalog     # Build the catalog's CSS and serve it at http://localhost:8000/catalog
composer docs        # Regenerate docs/components from the catalog
```

### Catalog

The catalog is a docs-style site, like shadcn/ui's, for browsing and trying every component. Each component page has a live playground (the real component, re-rendered on the server as you change props), examples with Preview/Code tabs, and an API reference. There's also a theme customiser that generates the `@theme` overrides.

It runs on [Testbench Workbench](https://packages.tools/workbench) and lives in `workbench/`. It is not shipped with the package. To add a component, add an entry to `workbench/app/Catalog.php`, then run `composer docs`; the Markdown in `docs/components` is generated from the catalog, and CI fails if it's out of date. Example code is rendered as-is, so what you read is exactly what ran. While editing styles, run `npm run catalog:watch` in a second terminal.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Please report security issues privately, as described in [SECURITY.md](SECURITY.md).

## License

MIT. See [LICENSE](LICENSE).
