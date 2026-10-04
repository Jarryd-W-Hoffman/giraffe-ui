# Components

_Generated from `workbench/app/Catalog.php` by `composer docs`. Run `composer catalog` to try them live._

## Actions

- [Button](button.md): Displays a button, or a link that looks like one.

## Forms

- [Field](field.md): Wraps a control with a label, hint and error message, and links them for screen readers.
- [Input](input.md): A text input that works on its own or inside a field.
- [Textarea](textarea.md): A multi-line text input.
- [Select](select.md): A native select, styled to match the other controls.
- [Combobox](combobox.md): A select you can type into to filter. Follows the WAI-ARIA combobox pattern. Needs Alpine, which Livewire includes.
- [Date picker](date-picker.md): A native date input with a calendar. Follows the WAI-ARIA date picker dialog pattern.
- [File upload](file-upload.md): A drop zone around a real file input, with the chosen files listed and Livewire upload progress.
- [Checkbox](checkbox.md): A native checkbox with a label and optional description.
- [Radio](radio.md): A native radio button. Group them in a fieldset.
- [Segmented control](segmented-control.md): A row of options where one is always picked, like a view switcher. Native radios underneath, so no JavaScript.
- [Switch](switch.md): An on/off toggle. A real checkbox with role="switch" underneath.
- [Fieldset](fieldset.md): Groups related controls under a legend, with a shared hint and error.
- [Label](label.md): A form label. Field uses it for you; use it directly for custom layouts.

## Display

- [Alert](alert.md): A message that stands out from the page, with an icon, title and actions.
- [Avatar](avatar.md): A profile image, with initials when there is no image.
- [Avatar group](avatar-group.md): Overlapping avatars, with a count of the ones not shown.
- [Description list](description-list.md): Terms and their details, like the fields of a record. A real dl, in two columns from the sm breakpoint.
- [Stat](stat.md): A headline number with a label and an optional change.
- [Badge](badge.md): A small label for status, counts or categories.
- [Spinner](spinner.md): Shows that something is loading. Buttons use it for their loading state.
- [Icon](icon.md): Heroicons that GiraffeUI ships with, or any icon from a blade-icons set.
- [Progress](progress.md): Shows how far along a task is, or that it is working when you can't tell.
- [Kbd](kbd.md): A keyboard key, for shortcuts and instructions.
- [Skeleton](skeleton.md): A placeholder shaped like the content that is loading.
- [Empty state](empty-state.md): What to show when there is nothing to show yet, with a way forward.
- [Card](card.md): A surface for grouping content, with an optional header and footer.
- [Separator](separator.md): A line that divides content, optionally with a label.

## Navigation

- [Breadcrumbs](breadcrumbs.md): Shows where the current page sits in the site. Follows the WAI-ARIA breadcrumb pattern.

## Overlays

- [Modal](modal.md): A dialog over the page. Built on the native &lt;dialog&gt; element, so focus trapping, Escape and the backdrop work without JavaScript.
- [Drawer](drawer.md): A panel that slides in from the side. The same native &lt;dialog&gt; as Modal, so focus trapping, Escape and the backdrop work without JavaScript.
- [Command palette](command-palette.md): Search pages and run actions from the keyboard with ⌘K / Ctrl+K. This catalog has one: try it.
- [Dropdown](dropdown.md): A menu of actions. A native popover positioned with CSS anchor positioning, with the menu keyboard pattern added by Alpine.
- [Popover](popover.md): Rich content in a floating panel. A native popover, so Escape and clicking outside close it without JavaScript.
- [Tooltip](tooltip.md): A short label shown on hover and keyboard focus. Escape dismisses it, and you can hover over it.
- [Toast](toasts.md): Brief, non-blocking messages announced to screen readers. Place &lt;x-gui::toasts /&gt; once in your layout.

## Disclosure

- [Accordion](accordion.md): Sections that expand and collapse. Built on native &lt;details&gt;, so it works without JavaScript.
- [Tabs](tabs.md): Switch between panels of related content. Follows the WAI-ARIA tabs pattern. Needs Alpine, which Livewire includes.

## Data

- [Table](table.md): Rows of data, from an array or composed cell by cell, with sorting, an empty state and pagination.
- [Pagination](pagination.md): Page links for any Laravel paginator, with a Livewire version.
