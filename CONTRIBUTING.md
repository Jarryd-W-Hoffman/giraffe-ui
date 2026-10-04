# Contributing

Thanks for helping. Bug reports, component ideas and pull requests are all welcome.

## Setup

```bash
composer install
npm install
composer catalog   # http://localhost:8000/catalog
```

## Before opening a pull request

```bash
composer test      # Pest
composer lint      # Pint
composer docs      # if you changed workbench/app/Catalog.php
npm run audit:a11y # with the catalog running: axe-core on every page, light and dark
```

Then check your change in the catalog in light and dark mode, using only the keyboard.

## Sign your commits

Every commit needs a `Signed-off-by` line ([Developer Certificate of Origin](https://developercertificate.org)). It certifies that you wrote the change, or have the right to submit it. Add it with:

```bash
git commit -s
```

## How components are built

- **Anonymous Blade components** live in `resources/views/components`. Use a class in `src/Components` only when a component needs PHP, or when its children need its state.
- **Children read parent state through one uniquely named `@aware` key,** like `guiField` or `guiModal`. Never use `@aware(['id'])` or similar: it picks up attributes from *any* parent component.
- **Write every Tailwind class out in full.** Tailwind finds classes by scanning files, so `"bg-gui-{$color}"` is never generated. Use a lookup array of complete class strings.
- **Theme with tokens:** use `--color-gui-*` and `--radius-gui`, never fixed brand colours. Text on the page uses the `-text` tokens.
- **Pass other attributes through** to the component's main element (`class`, `wire:*`, `x-*`).
- **Platform first.** Prefer native elements and APIs (`<dialog>`, `<details>`, popovers, anchor positioning, `command`/`commandfor`). Use Alpine only for behaviour the browser doesn't have.
- **Accessibility is part of the feature.** Follow the [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/patterns/) for keyboard and ARIA. New colour pairings need 4.5:1 contrast for text and 3:1 for borders and focus rings, in both modes.
- **Add the component to the catalog** (`workbench/app/Catalog.php`) with examples and a props table, then run `composer docs`.
