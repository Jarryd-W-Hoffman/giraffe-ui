# Changelog

All notable changes to GiraffeUI are documented here. This project follows [Semantic Versioning](https://semver.org).

## [Unreleased]

## [0.1.0] - Unreleased

The first release: a rebuild on Tailwind CSS v4 and Laravel 13, with 35 components and a catalog.

### Textarea autosize and character count

#### Added
- Textarea `autosize`: grows with its content using CSS `field-sizing`, with no JavaScript. `rows` becomes the minimum height and `max-rows` caps it. Browsers without `field-sizing` keep the fixed height and the resize handle.
- Textarea `counter`: a character count, against `maxlength` when there is one. The limit is part of the accessible description and the visible count is hidden from screen readers. A polite live region says how many characters are left once typing pauses. The count also follows values set by `wire:model`, `x-model`, scripts and form resets.
- The catalog's textarea page has a playground, and the Livewire demo form has a bio with a counter.

### File upload

#### Added
- File upload: a drop zone with a real `<input type="file">` stretched invisibly across it. Clicking and dropping are native, it works without JavaScript, and the field's label stays the only accessible name. Alpine adds a list of chosen files with sizes, and a progress bar from Livewire's upload events. It works in a Field (label, hint, errors), and `wire:model` goes on the input.
- A Livewire upload demo in the catalog.
- `[x-cloak]` is hidden in `giraffeui.css`.

### Command palette

#### Added
- Command palette: ⌘K / Ctrl+K opens a native `<dialog>` with a search box (a combobox using `aria-activedescendant`) over grouped options. Typing filters by text and `keywords`, and groups with no matches hide. Arrow keys, Home and End move; Enter or a click activates the item, a real link or button, so `href`, `wire:click` and `x-on:click` all work. The palette closes before an item's own click handler runs. Items take an icon and a shortcut hint.
- The catalog has its own palette: search every page and switch theme.

#### Fixed
- Combobox: pressing Enter straight after typing could pick the previously highlighted option, because the highlight updates a tick later. Enter now uses the first match when the highlight is stale. The palette had the same race and the same fix.
- Catalog: Livewire requests failed when Testbench regenerated its skeleton `.env` with a database cache, so the workbench now sets file cache and sessions itself.

### Drawer, Progress and Kbd

#### Added
- Drawer: a modal that slides in from the right or left. It's the same native `<dialog>` (focus trapping, Escape, backdrop, `command`/`commandfor`, `modal.close`, `wire:ignore.self`), full height, with a pinned footer.
- Progress: determinate or indeterminate, with `role="progressbar"`, `aria-valuenow` (values clamped), a label used as the accessible name, theme colours, and an optional percentage. The indeterminate bar stops moving with reduced motion.
- Kbd for keyboard keys.

### Icon

#### Added
- Icon: the Heroicons GiraffeUI uses (MIT), with `xs`/`sm`/`md`/`lg` sizes. Icons are hidden from screen readers unless given a `label`. Any other name goes to blade-icons when it's installed, so any icon set works. An unknown name without blade-icons gives an error listing the bundled icons.

#### Changed
- Components use the Icon component instead of copies of the same SVG markup. A pixel-by-pixel comparison of every catalog page in both themes, and of open overlays, shows no visual change.

### Breadcrumbs, skeletons and empty states

#### Added
- Breadcrumbs, following the WAI-ARIA breadcrumb pattern: a labelled `nav` with an ordered list, `aria-current="page"` on the current item, and decorative separators. Pass `items` or compose `breadcrumbs.item`.
- Skeleton loading placeholders, hidden from screen readers, sized with classes, with a `lines` option. They stop pulsing with reduced motion.
- Empty state with a title, description, icon and actions.

#### Fixed
- Catalog examples that override a component's own classes (icon-only buttons, a round skeleton, a compact empty state) now use Tailwind's `!` modifier. Without it the component's class could win, e.g. `px-0` lost to the button's `px-3`. The convention is documented.

### Accessibility audit

#### Added
- `npm run audit:a11y` runs axe-core (WCAG 2.2 A and AA) on every catalog page in light and dark mode, and CI runs it on every pull request.

#### Changed
- Disabled checkboxes, radios and switches dim the control and use lighter (still 4.5:1) text, instead of fading the whole row to 60% opacity, which took descriptions down to 2.9:1.

### Combobox

#### Added
- Combobox: a select you can type into to filter, following the WAI-ARIA combobox pattern. Focus stays in the input with `aria-activedescendant`. Arrow keys open the list onto the current option; Enter picks; Escape closes, then clears. The list is a top-layer popover anchored to the input, the same width and flipping when there's no room, so modals and scroll containers don't clip it. It works in a Field (label, hint, errors), submits through a hidden input, and binds with `wire:model`/`x-model`. Needs Alpine.

### Livewire and loading states

#### Added
- Spinner component.
- Button loading state. Buttons with `wire:click`, and submit buttons in a `wire:submit` form, show a spinner and ignore clicks while their request runs, using the `data-loading` attribute Livewire 4 adds. No `wire:loading` or `wire:target` needed. Outside Livewire, set `loading`.
- A Livewire guide in the catalog with live Livewire 4 demos (validated form, searchable and sortable paginated table, `wire:model` tabs), and Livewire tests for each.

#### Fixed
- Table, Card, Alert, Modal and the choice controls treated a slot as non-empty when it held only Livewire's `<!--[if BLOCK]>` markers, so a composed table showed no empty state under Livewire.
- `gui:install` now checks the app's `composer.json` for Livewire instead of whether the class exists, which was true in any environment with Livewire installed (such as GiraffeUI's own development).

### Phase 3: data

#### Added
- Table v2:
  - **Sorting:** `sortable`, `sort` and `direction`. Sortable headings link to `?sort=…&direction=…` (dropping `page`) and set `aria-sort`.
  - **Composed tables:** build one from `table.heading`, `table.row` and `table.cell` for custom cells. A composed heading puts `wire:click`/`x-on:click` on its sort button, for Livewire sorting.
  - **Footer and pagination:** a `footer` slot, and automatic pagination when `rows` is a paginator.
- Pagination: `<x-gui::pagination :paginator="$users" />` for length-aware and simple paginators, with a summary, an accessible current page, and page numbers that collapse on small screens. There's a `livewire` mode using `gotoPage`/`previousPage`/`nextPage`, and both views can be used with `->links()`, `Paginator::defaultView()` or Livewire's `paginationView()`.

#### Changed
- Table is now a class component, so composed headings can read the sort through a unique `@aware` key. The `columns` and `rows` API is unchanged.

### Phase 2b: popovers, menus, tooltips and toasts

#### Added
- Dropdown: a menu in a native popover, positioned with CSS anchor positioning (Baseline since January 2026) and flipping when there's no room. Escape, Tab and outside clicks close it and return focus. Alpine adds the menu keyboard pattern: focus the first item on open, arrow keys skipping disabled items, Home/End. Items close the menu with `command="hide-popover"` after their own click handlers run, or use their own command (e.g. to open a modal). Items can be buttons or links, danger or disabled, and there are separators.
- Popover: rich content in an anchored native popover, with 8 positions.
- Tooltip: shown with CSS on hover and keyboard focus after a short delay, and stays open while the pointer is over it. Alpine adds `aria-describedby` and Escape-to-dismiss (WCAG 1.4.13).
- Toasts: `<x-gui::toasts />` once in a layout, then `GiraffeUI.toast()`, `$dispatch('toast')`, Livewire's `$this->dispatch('toast')` or `session()->flash('toast')`. A polite live region in the top layer, so toasts appear above modals and are still announced. Hovering or focusing a toast pauses it, and `duration: 0` keeps it until dismissed.
- `JayAitch\GiraffeUI\Support\Anchor` for anchor names and placement. `giraffeui.css` now also scans `src/` for classes.

#### Fixed
- Catalog example headings use `example-` ids, so they can't collide with ids in the examples (the "Share" heading was hijacking the Share popover).

### Phase 2a: overlays and disclosure

#### Added
- Modal, built on native `<dialog>`: the browser handles focus trapping, Escape, the backdrop and returning focus. Open and close it with `command`/`commandfor` buttons (Baseline since Safari 26.2) and no JavaScript. Has title, description, sizes, a scrolling body, a footer, `<x-gui::modal.close>`, `:dismissible="false"` and `:open` for reopening after validation errors. `wire:ignore.self` keeps it open across Livewire re-renders.
- Accordion, built on native `<details>`. `exclusive` uses a shared `name` so the browser keeps one item open. Works without JavaScript.
- Tabs, following the WAI-ARIA tabs pattern: arrow keys, Home/End, roving tabindex, and linked tab/panel ids. The selected tab is rendered on the server, so there's no flash before Alpine starts. Supports `wire:model` via `x-modelable`, and underline and pills variants. Needs Alpine (Livewire includes it).
- Catalog pages for all three, and usage notes in the catalog and generated docs.

### Phase 1: forms and display

#### Added
- Form components: Field, Label, Input, Textarea, Select, Checkbox, Radio, Switch and Fieldset.
  - Field links its label, hint and error to the control with `for`/`id` and `aria-describedby`, and passes down `name` and `required`.
  - Controls set `aria-invalid` from Laravel's error bag (by `name`, or by `wire:model` when there's no name), and refill old input except for passwords and files.
  - Array names work: `address[city]` gets the id `gui-address-city` and the error key `address.city`.
- Display components: Alert, Avatar, Badge, Card and Separator.
- `--color-gui-input` token for input borders and switch tracks. zinc-500 is the lightest shade with 3:1 contrast in both modes.
- Catalog pages for every component, grouped in the sidebar, with playgrounds for Input, Alert, Avatar and Badge.
- `composer docs` generates `docs/components/*.md` from the catalog. CI checks they're up to date.

### Phase 0: foundation

A rebuild on Tailwind CSS v4 and Laravel 13.

#### Added
- `resources/css/giraffeui.css`: one file to import, with `@theme` colour and radius tokens (`--color-gui-*`, `--radius-gui`).
- `--color-gui-*-text` tokens for outline/ghost text, borders and focus rings, with lighter dark-mode shades. Every default colour pairing meets WCAG AA in light and dark mode.
- `leading` and `trailing` slots on the button, for icons.
- An empty state for the table (`No results.` by default, or an `empty` slot).
- A component catalog (`composer catalog`) with live playgrounds, Preview/Code examples, API references, a theme customiser and dark mode.
- Tests (Pest + Testbench) and GitHub Actions CI, including a check that Tailwind finds every component class.

#### Changed
- Requires PHP 8.4+ and Laravel 13.
- Components are now anonymous Blade components. Usage is unchanged: `<x-gui::button>`, `<x-gui::table>`.
- Button: `link` is now `href`; variants are `solid`, `outline` and `ghost`; sizes are `sm`, `md` and `lg`.
- Button links no longer add `wire:navigate` automatically. Add it yourself if you want it.
- Table: `columns` can be a list of keys or a `key => label` map, and nested keys like `team.name` work. Rows can be arrays, objects or models.
- `gui:install` now only adds GiraffeUI's CSS import, offers Livewire (optional) and publishes a layout. It never runs npm or edits Tailwind config files, and is safe to run again.
- The published layout is an anonymous component (`<x-layouts.app>`) with flash-free dark mode and no Alpine or Livewire requirement.

#### Removed
- `config/giraffeui.php`. Theme with CSS variables instead.
- Button props `fullWidth` (use `class="w-full"`), `event` (use `x-on:click`, `wire:click` or `onclick` directly) and the unused `text`, `customLeft` and `customRight`.
- Table prop `hiddenColumns`. Only list the columns you want.
- Installer support for Tailwind v3, Volt and npm/yarn installs.

#### Fixed
- Button classes running together (e.g. `text-smw-full`).
- Outline and text button colours missing from production CSS because their class names were built at runtime.
- Unescaped `onclick` output on every button.
- The installer overwriting an existing `tailwind.config.js` and never creating one when it was missing.
- The installer running `yarn install` instead of `yarn add`.
- The service provider registering a `GiraffeUI` class that didn't exist.
