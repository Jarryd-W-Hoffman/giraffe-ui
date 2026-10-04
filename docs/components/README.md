# Components

_Generated from `workbench/app/Catalog.php` by `composer docs`. Run `composer catalog` to try them live._

## Actions

- [Button](button.md): Displays a button, or a link that looks like one.

## Forms

- [Field](field.md): Wraps a control with a label, hint and error message, and links them for screen readers.
- [Input](input.md): A text input that works on its own or inside a field.
- [Textarea](textarea.md): A multi-line text input.
- [Select](select.md): A native select, styled to match the other controls.
- [Checkbox](checkbox.md): A native checkbox with a label and optional description.
- [Radio](radio.md): A native radio button. Group them in a fieldset.
- [Switch](switch.md): An on/off toggle. A real checkbox with role="switch" underneath.
- [Fieldset](fieldset.md): Groups related controls under a legend, with a shared hint and error.
- [Label](label.md): A form label. Field uses it for you; use it directly for custom layouts.

## Display

- [Alert](alert.md): A message that stands out from the page, with an icon, title and actions.
- [Avatar](avatar.md): A profile image, with initials when there is no image.
- [Badge](badge.md): A small label for status, counts or categories.
- [Card](card.md): A surface for grouping content, with an optional header and footer.
- [Separator](separator.md): A line that divides content, optionally with a label.

## Overlays

- [Modal](modal.md): A dialog over the page. Built on the native &lt;dialog&gt; element, so focus trapping, Escape and the backdrop work without JavaScript.
- [Dropdown](dropdown.md): A menu of actions. A native popover positioned with CSS anchor positioning, with the menu keyboard pattern added by Alpine.
- [Popover](popover.md): Rich content in a floating panel. A native popover, so Escape and clicking outside close it without JavaScript.
- [Tooltip](tooltip.md): A short label shown on hover and keyboard focus. Escape dismisses it, and you can hover over it.
- [Toast](toasts.md): Brief, non-blocking messages announced to screen readers. Place &lt;x-gui::toasts /&gt; once in your layout.

## Disclosure

- [Accordion](accordion.md): Sections that expand and collapse. Built on native &lt;details&gt;, so it works without JavaScript.
- [Tabs](tabs.md): Switch between panels of related content. Follows the WAI-ARIA tabs pattern. Needs Alpine, which Livewire includes.

## Data

- [Table](table.md): Displays rows of data with headings and an empty state.
