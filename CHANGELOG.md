# Changelog

All notable changes to GiraffeUI are documented here. This project follows [Semantic Versioning](https://semver.org).

## [Unreleased]

Phase 0: a rebuild on Tailwind CSS v4 and Laravel 13.

### Added
- `resources/css/giraffeui.css`: one file to import, with `@theme` colour and radius tokens (`--color-gui-*`, `--radius-gui`).
- `leading` and `trailing` slots on the button, for icons.
- An empty state for the table (`No results.` by default, or an `empty` slot).
- Tests (Pest + Testbench) and GitHub Actions CI, including a check that Tailwind finds every component class.

### Changed
- Requires PHP 8.3+ and Laravel 13.
- Components are now anonymous Blade components. Usage is unchanged: `<x-gui::button>`, `<x-gui::table>`.
- Button: `link` is now `href`; variants are `solid`, `outline` and `ghost`; sizes are `sm`, `md` and `lg`.
- Button links no longer add `wire:navigate` automatically. Add it yourself if you want it.
- Table: `columns` can be a list of keys or a `key => label` map, and nested keys like `team.name` work. Rows can be arrays, objects or models.
- `gui:install` now only adds GiraffeUI's CSS import, offers Livewire (optional) and publishes a layout. It never runs npm or edits Tailwind config files, and is safe to run again.
- The published layout is an anonymous component (`<x-layouts.app>`) with flash-free dark mode and no Alpine or Livewire requirement.

### Removed
- `config/giraffeui.php`. Theme with CSS variables instead.
- Button props `fullWidth` (use `class="w-full"`), `event` (use `x-on:click`, `wire:click` or `onclick` directly) and the unused `text`, `customLeft` and `customRight`.
- Table prop `hiddenColumns`. Only list the columns you want.
- Installer support for Tailwind v3, Volt and npm/yarn installs.

### Fixed
- Button classes running together (e.g. `text-smw-full`).
- Outline and text button colours missing from production CSS because their class names were built at runtime.
- Unescaped `onclick` output on every button.
- The installer overwriting an existing `tailwind.config.js` and never creating one when it was missing.
- The installer running `yarn install` instead of `yarn add`.
- The service provider registering a `GiraffeUI` class that didn't exist.
