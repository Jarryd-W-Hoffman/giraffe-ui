# Footer

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

A site footer: your brand and a line about it, columns of links, and the fine print.

```blade
<x-gui::footer :links="['Docs' => ['Install' => '/install']]" />
```

## Basic

links is heading =&gt; [text =&gt; href]; each column is a nav named by its heading. container sets the width.

```blade
<x-gui::footer
    class="w-full"
    description="Accessible components for Laravel."
    :links="[
        'Docs' => ['Introduction' => '#', 'Installation' => '#'],
        'Company' => ['About' => '#', 'Blog' => '#'],
        'Legal' => ['Privacy' => '#', 'Terms' => '#'],
    ]"
>
    <x-slot:brand>Acme</x-slot:brand>
    <x-slot:legal>© 2026 Acme. All rights reserved.</x-slot:legal>
</x-gui::footer>
```

## Simple

One row: brand, every link, and the fine print. Good under app pages.

```blade
<x-gui::footer variant="simple" class="w-full" :links="['Docs' => ['Docs' => '#', 'Changelog' => '#'], 'Legal' => ['Privacy' => '#', 'Terms' => '#']]">
    <x-slot:brand>Acme</x-slot:brand>
    <x-slot:legal>© 2026 Acme</x-slot:legal>
</x-gui::footer>
```

## Centred, with social links

Everything centred. The social slot holds icon links; give each an aria-label.

```blade
<x-gui::footer variant="centered" class="w-full" description="Accessible components for Laravel." :links="['Product' => ['Features' => '#', 'Pricing' => '#'], 'Company' => ['About' => '#', 'Blog' => '#', 'Careers' => '#']]">
    <x-slot:brand>Acme</x-slot:brand>
    <x-slot:social>
        <x-gui::button href="#" variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Email us"><x-gui::icon name="envelope" size="sm" /></x-gui::button>
        <x-gui::button href="#" variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="RSS feed"><x-gui::icon name="rss" size="sm" /></x-gui::button>
        <x-gui::button href="#" variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Our website"><x-gui::icon name="globe-alt" size="sm" /></x-gui::button>
    </x-slot:social>
    <x-slot:legal>© 2026 Acme. All rights reserved.</x-slot:legal>
</x-gui::footer>
```

## With a newsletter

Columns, with an email sign-up beside them. It's a real form that posts the email to newsletter-action.

```blade
<x-gui::footer variant="newsletter" class="w-full" newsletter-action="#" :links="['Docs' => ['Introduction' => '#', 'Installation' => '#'], 'Company' => ['About' => '#', 'Blog' => '#'], 'Legal' => ['Privacy' => '#', 'Terms' => '#']]">
    <x-slot:brand>Acme</x-slot:brand>
    <x-slot:newsletter-text>One email a month. No spam.</x-slot:newsletter-text>
    <x-slot:legal>© 2026 Acme</x-slot:legal>
</x-gui::footer>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `links` | `array` | `[]` | heading =&gt; [text =&gt; href]. |
| `description` | `string` | `null` | A line under the brand. |
| `container` | `string` | `'max-w-6xl'` | Width classes for the contents. |
| `variant` | `'columns' \| 'simple' \| 'centered' \| 'newsletter'` | `'columns'` | The layout. |
| `newsletter, newsletter-action, newsletter-button` | `string` | `'Get the newsletter', '#', 'Subscribe'` | For the newsletter variant (translatable). |

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `brand` | Your logo and name. |
| `legal` | The fine print along the bottom. |
| `social` | Icon links, for every variant but simple. |
| `newsletter-text` | A line under the newsletter heading. |
