# Changelog

All notable changes to GiraffeUI are documented here. This project follows [Semantic Versioning](https://semver.org).

## [Unreleased]

## [0.1.0] - Unreleased

The first release: a rebuild on Tailwind CSS v4 and Laravel 13, with 61 components and a catalog.

### Charts: the data table is optional

#### Changed
- Line, area, bar and scatter charts hide "Show the data" by default. Add `table` to show it.
  - Without it, the same table of exact numbers is still in the page for screen readers, so the charts keep a text equivalent.

### Inbox example

#### Added
- Inbox example, in the workspace shell:
  - conversations with search, an All/Unread filter, labels with dots, stars and unread marks;
  - a reading pane with the thread, and star, mark-as-unread and archive buttons;
  - a reply box that adds your message, and a Compose modal;
  - on phones the list and the conversation take turns, with a back button.
- Icon: `archive-box` (167 bundled).
- Landing page: the examples gallery shows six apps: Dashboard, Projects, Inbox, Customers (Onboarding without Livewire), Sign in and Pricing.

#### Changed
- The examples' "Back to the catalog" link hides when a page is shown in a frame, such as the landing page's previews.

### Logo

#### Added
- A logo: a rounded U whose right arm is a giraffe's neck, its head curving over the opening. It's in `art/logo.svg` and appears in the README, in the catalog and landing page headers, and as the favicon.

### Landing page

#### Added
- Catalog: a landing page at `/` (and `/catalog/home`), built from GiraffeUI components. It has:
  - a hero with the install command and a copy button;
  - a wall of working components: sign-up form, OTP input, rating, a line chart, switches, date and time pickers, team list, plan cards and a donut;
  - the numbers and a feature grid;
  - a live theming playground: primary colour and corners restyle a preview, the switch toggles dark mode, and the matching `@theme` code is ready to copy;
  - a code-and-preview section with a working modal;
  - live, scaled-down previews of the example apps;
  - a Livewire section, a closing call to action and a footer;
  - ⌘K search over every component and guide.

### New look for every example

#### Added
- Button:
  - `shape="pill"`;
  - `color="ink"`, the page's own contrast: black on light, white on dark, and a soft grey as ghost;
  - `icon` for square (or round) icon-only buttons.
- Badge: `shape="pill"`, and `dot` for a small coloured mark before the label.
- Card: `variant="filled"`, borderless and a step off the page with rounder corners, and `icon` before the title.
- Projects example: a project board in a workspace with the collapsible sidebar, submenus and a collapsible group.
- Sidebar item: without an `href` it's a button, e.g. to open a modal with `commandfor`.

#### Changed
- Every example uses the dashboard's style: black (or white) pages, pill navigation and buttons, filled cards and large light headings.
  - Dashboard, Projects and Customers share one workspace shell: the collapsible sidebar, search and notifications. Settings opens as a modal from the sidebar or the account menu.
  - Sign in, Register and Forgot password are split pages with a gradient panel.
  - Pricing, Onboarding and Not found are restyled to match.
- The Dashboard example adds stat tiles with sparklines, revenue by channel, goals, top products, payment methods and upcoming payments.
- The examples use GiraffeUI components for these parts, not one-off markup.

### Dashboard redesign

#### Added
- Navbar: `variant="pills"`, where each link is a rounded pill and the current one is filled.
- Line chart:
  - `dashed` draws series dashed and behind the others, for comparisons.
  - `colors` takes `muted` for a receding grey.
  - `axis="false"` drops the y axis and gridlines.
  - Area fills now fade towards the baseline.

#### Changed
- The Dashboard example is redesigned as a dark-first analytics page. It has:
  - a pill navbar and a welcome headline;
  - total revenue with Transfer and Request buttons;
  - a split bar of spending by category;
  - cash flow (income against dashed expenses), activity by time as a heatmap, and recent transactions with category pills.
- The heatmap shows every column label when there are eight or fewer.
- The sidebar's catalog examples and browser test now use the Projects example.

### Sidebar: collapsing and sub-sections

#### Added
- Sidebar layout: `collapsible` adds a Collapse sidebar button that shrinks the column to a rail of icons, from the lg breakpoint.
  - In the rail, labels stay for screen readers, and show as tooltips on hover and keyboard focus. Items without an icon show their first letter.
  - The choice is kept in localStorage (`persist`) and applied before the page paints. `collapsed` sets the starting state.
- Sidebar submenu (`sidebar.submenu`): items nested under a parent, as a native disclosure that works without JavaScript. It opens when it holds the current page.
- Sidebar group: `collapsible` lets its heading show and hide it.
- In the rail, clicking a submenu or a closed group expands the sidebar and opens it.
- Icon: `chevron-double-left`. Translations: `collapse_sidebar`, `expand_sidebar`.

#### Changed
- The examples use a collapsible sidebar, with a Projects submenu and a collapsible group.
### More icons

#### Added
- Icons: 167 bundled Heroicons (up from 38), with no packages needed. They're generated from the Heroicons source and cover:
  - money (`banknotes`, `credit-card`, `wallet`, `receipt-percent`);
  - charts (`chart-bar`, `chart-pie`, `presentation-chart-line`, `arrow-trending-up`);
  - messages (`envelope`, `bell`, `chat-bubble-left-right`, `inbox`);
  - people, files, commerce, media and devices;
  - more arrows, plus `ellipsis-vertical`.
- `Icons::SHAPES` draws icons whose shapes use different fill rules shape by shape, so they render exactly as designed.
- Catalog: the icon grid has a filter.

### Bar list, scatter chart and heatmap

#### Added
- Bar list (`chart.bar-list`): a ranked list with a soft bar behind each name, for top pages, referrers and countries.
  - Rows can link, are sorted largest first, and take `limit` and `percent` for each row's share.
- Scatter chart (`chart.scatter`): how two measures relate, with axis titles and a point label in the tooltip and table.
  - Each series has its own colour and marker shape (circle, square, diamond, triangle), so series are told apart without colour.
  - Hover snaps to the nearest dot, and the arrow keys step through the dots left to right.
- Heatmap (`chart.heatmap`): a table of values shaded in five steps of one hue, mixed into the chart surface so it suits light and dark mode, with a scale below.
  - Screen readers move through it as a table; hover or arrow keys show each cell.
- Translations: `series`, `scatter_summary`, `heatmap_summary`.

### Timeline

#### Added
- Timeline: events in order (an order's history, an activity feed) as an ordered list, with `timeline.item` for each event.
  - Each item has a `title`, a `time` (text or a date object, shown in a `<time>` element with `datetime`) and any content below.
  - Its marker is an `icon` in a coloured circle, a small dot, or your own in the `marker` slot, such as an avatar. A line joins each event to the next.

### Time and datetime pickers

#### Added
- Time picker: a native time input (it works without JavaScript and submits H:i) with a clock button that opens a list of times every `interval` minutes, between `min` and `max`.
  - The list follows the WAI-ARIA listbox pattern: it opens on the chosen time (or the next one), arrow keys, Page Up/Down, Home and End move, and Enter picks.
  - Times are written the way the visitor's locale writes them (2:30 PM, or 14:30), or as `locale` sets.
  - Picking fires input and change, so `wire:model` and `x-model` update.
- Datetime picker: a Date picker and a Time picker side by side, submitting `name[date]` and `name[time]`.
  - It takes a date object, "Y-m-d H:i" or an array as its value, plus `min`/`max` dates, `min-time`/`max-time`, `interval` and disabled dates.
  - `wire:model="startsAt"` binds `startsAt.date` and `startsAt.time`, and each part's errors show under the pair.
- Icon: `clock`. Translations: `choose_time`, `date`, `time`.

#### Fixed
- Catalog: pages for components without slots no longer show an empty Slots table.

### Copy button

#### Added
- Copy button: copies `value`, or the text of a `target` (an input's value, or an element's text), and confirms with a check, a "Copied" label and a status message for screen readers.
  - Icon only by default; with a label, it takes the Button's variant, color and size.
  - Falls back to the older copy command where the Clipboard API isn't available (plain HTTP), and dispatches a `copied` event with the text.
- Input: `copyable` puts a Copy button inside the field.
- Icon: `document-duplicate`. Translations: `copy`, `copied`, `copy_failed`.

### Charts

#### Added
- Charts drawn on the server, with no chart library: SVG for the marks and HTML for every label, so text stays its real size at any width. Alpine adds the hover and keyboard layer; without it they still render in full.
  - Line chart (`chart.line`): one series or several, gaps for missing values, rounded y ticks from zero, a legend for two or more lines and labels at their ends when there's room. Also `area`, `stacked`, `curve` (monotone, never overshoots), `markers`, `highlight` (one series in colour, the rest grey), `reference` and `reference-label`.
  - Area chart (`chart.area`): a line chart with its fill on; several series stack by default.
  - Bar chart (`chart.bar`): single, grouped, `stacked` or `horizontal`, with negative values from a zero line, thin bars with rounded data ends, a 2px gap between touching bars and segments, and value labels for a single series.
  - Donut chart (`chart.donut`): a list beside it is the legend and the numbers in one; past eight parts the rest fold into "Other".
  - Sparkline: a small trend line for stats and table rows, with a spoken summary of its start, end, high and low.
  - Every chart is a focusable group: the arrow keys step through the points and a live region reads each series there. The crosshair or tooltip lists every series, "Show the data" opens the exact numbers as a table, and an empty chart says "No data yet."
  - Charts adapt to their container's width, not the screen's.
- Theme tokens `--color-gui-chart-1` to `8`, a categorical palette validated for colour-blind readers in light and dark, plus `--gui-chart-surface` and `--gui-chart-muted`. Translations: `trend`, `chart_summary`, `show_data`, `no_data`, `point`, `total`, `other`, `share`.

#### Changed
- The Dashboard example has sparklines in its stats, a revenue line chart, a payment-methods donut and an orders bar chart.

### Theme switcher

#### Added
- Theme switcher: Light / Dark / System as native radios (icons and optional labels), or `variant="toggle"`, a single button with `aria-pressed`.
  - It saves the choice through the layout's `GiraffeUI.setTheme()`, defining it if the layout doesn't, and follows the OS while on System.
  - Every switcher on the page stays in sync, however the theme changed.
- Icons: `sun`, `moon`, `computer-desktop`. Translations: `theme`, `light`, `dark`, `system`, `dark_mode`.

#### Changed
- The Settings example and the catalog header use it instead of their own switchers.

### Rating

#### Added
- Rating: pick a number of stars, as a native radio group.
  - One tab stop, arrow keys, `required` and form submission all come from the browser. Each star is read out as "3 stars", and in a field the group is named by the label.
  - The stars fill and preview on hover with CSS alone.
  - `readonly` shows a value instead, with partial stars for fractions ("Rated 4.5 out of 5").
  - Also `max`, `size`, `disabled`, `wire:model` / `x-model`, and old input.
- Icon: `star`. Translations: `star`, `stars`, `rated`.

### Safari (WebKit) testing

#### Added
- CI runs the browser behaviour tests and the axe audit in WebKit, the engine behind Safari, alongside Chromium and Firefox. Both scripts take `BROWSER=webkit`, and CONTRIBUTING shows how to run it locally in Playwright's Docker image.

#### Fixed
- Catalog: the playground's selects were unreadable in dark mode in WebKit.

### Banner

#### Added
- Banner: a full-width notice for the top of a page, `solid` or `soft` in any colour, with an icon and an `action` slot. It's a labelled region (translatable "Announcement").
  - `dismissible` adds a close button.
  - `remember="key"` keeps a dismissed banner hidden on later visits in that browser, hidden before the page paints so it never flashes.
  - Dismissing fires `banner-dismissed` (`detail: { key }`) for recording it server-side.
- Translation: `announcement`.

### OTP input

#### Added
- OTP input: a one-time code or PIN shown as one box per character.
  - It's a single real input underneath (`autocomplete="one-time-code"`, a numeric keyboard, `maxlength` and `pattern`). Phone autofill, pasting, one tab stop, one submitted value, `wire:model` and screen readers all work as on any field.
  - Alpine draws the boxes, with the active one highlighted, and keeps only digits (or letters and digits with `alphanumeric`), up to `length`.
  - `autosubmit` submits the form when the code is complete. Codes are never refilled from old input.
  - Without JavaScript it's a plain text field.

### Navbar

#### Added
- Navbar: a top bar with `brand`, links (`navbar.item`) and `actions` slots.
  - Below the md breakpoint, the links fold into a menu: a native popover under the bar, so Escape and clicking outside close it and focus returns to the button, with no JavaScript.
  - Actions stay in the bar at every size.
  - Items mark the current page from the URL, or with `current`. `sticky` keeps it at the top over a blurred background.
- Translation: `menu`.

#### Changed
- The Pricing example uses the Navbar instead of its own header.

### Combobox: pick several

#### Added
- Combobox `multiple`: pick several options.
  - Each choice shows as a chip with its own remove button, and Backspace in an empty search removes the last one.
  - Picking toggles an option and keeps the list open (`aria-multiselectable`), and additions and removals are announced.
  - It submits `name[]`, refills from old input, and `wire:model` / `x-model` bind an array.
  - It works with `url`, and chips keep their labels when a search no longer includes them.
- Translations: `remove`, `added`, `removed`.

### Input prefixes, suffixes and a password Show button

#### Added
- Input `prefix` and `suffix`: text attached to either side, sharing the input's border, such as `acme.app/`, `$` or `kg`. Screen readers hear it through `aria-describedby`.
- Input `viewable` for `type="password"`: a Show button (eye icon) that toggles the password and reports its state with `aria-pressed`. It switches back to hidden when the form submits, so the browser never saves the password as text. `show-label` (translatable) names it.
- Icons: `eye`, `eye-slash`.

#### Changed
- The Sign in and Register examples use `viewable` instead of their own Show button, and Onboarding's web address uses `prefix`.

### Translations

#### Added
- Every piece of built-in text (labels such as "Close", "Choose date" and "Select all rows", messages such as "No results" and "The upload failed", counters, and the pagination sentence) now comes from Laravel's translator, so it follows the app's locale.
  - Publish it with `php artisan vendor:publish --tag=giraffeui-lang` and translate `lang/vendor/giraffeui/{locale}/ui.php`.
  - Props that set the same text still win.
  - Translated text is escaped where it's mixed with markup (pagination, file upload).
- A test checks that every key the components use is defined and every defined key is used.

### Icon with blade-icons

#### Fixed
- Icon: a blade-icons icon with a `label` was still hidden from screen readers, because icon sets like Heroicons ship `aria-hidden="true"` inside their SVG files. The component now drops the file's `aria-hidden` and decides itself: decorative icons get exactly one `aria-hidden="true"`, labelled ones get `role="img"` and `aria-label` only.

#### Added
- Tests for the blade-icons fallback, with `blade-ui-kit/blade-heroicons` as a dev dependency, and an "any blade-icons set" example on the Icon page when it's installed.
- CI's "Without optional packages" job (formerly "Without Livewire") also removes blade-icons, so the Icon component's install hint is tested too.

### Date picker: date ranges

#### Added
- Date picker `range`: two native date inputs under one name.
  - They submit `name[start]` and `name[end]`, refill from old input, and show errors at `name.start` and `name.end` under the control. `value` takes `['start' => …, 'end' => …]`, and `wire:model` or `x-model` on the picker binds `.start` and `.end`.
  - In the calendar, the first pick is the start and the second the end. The days between are highlighted, with a preview as you point or move, and a line says which end is next.
  - Picking a day before the start starts again from there. The end can't be before the start, and the browser checks that too.
  - The inputs are named by the field's label (as a group) plus `start-label` and `end-label`.
  - In Firefox, each input is clipped so its own picker icon doesn't show.
- Field labels have an id (`{id}-label`), so grouped controls can be named by them.

### Date picker: disabled dates and weekdays

#### Added
- Date picker `disabled-dates` (Y-m-d strings or dates) and `disabled-weekdays` (0 for Sunday to 6 for Saturday). The calendar crosses them out and won't pick them, and a typed one fails the browser's validation with `unavailable-message`. Validate them on the server too.

### Onboarding example

#### Added
- Catalog: an Onboarding full-page example. It's a four-step setup (workspace, team, preferences, review) built on the stepper and ordinary form posts, so it works without Livewire or even JavaScript.
  - Each step validates on the server and keeps its answers in the session.
  - Finished steps link back from the stepper, and the URL can't skip ahead.
  - It uses the server-searching combobox for the timezone.

### Table row selection, sticky header and alignment

#### Added
- `table.select` and `table.select-all`: row selection with real checkboxes.
  - Rows submit as `name[]` in a plain form, or bind with `wire:model` or `x-model`.
  - The header checkbox ticks or clears the page's rows, firing `input` and `change` on each so bindings follow (Livewire sends them in one request). It shows a mixed state when only some are ticked.
  - It keeps up when rows change, and when `wire:model`, `x-model` or scripts tick rows without an event.
  - Selected rows are highlighted with CSS `:has()`.
- Table `max-height`: the table scrolls within it under a sticky header, and the scroller is keyboard focusable while it overflows in either direction.
- `align` on `table.heading` and `table.cell` (`left`, `center`, `right`), and an `align` map for tables built from `columns`. Aligned headings drop the built-in `text-left` instead of needing `!`.

#### Changed
- The Customers example uses `table.select`, `table.select-all` and `align`, replacing its own selection code.

### Dropdown submenus, checkbox and radio items

#### Added
- `dropdown.checkbox` and `dropdown.radio`: real checkboxes and radios with the `menuitemcheckbox`/`menuitemradio` roles. They toggle natively (Space, click, and Enter too), submit with `name`/`value`, and work with `wire:model` or `x-model`. Toggling leaves the menu open, even through a Livewire update. Arrow keys move through the menu instead of changing the checked radio.
- `dropdown.group`: groups items under an optional `heading` that names the group for screen readers.
- `dropdown.submenu`: a nested popover beside its item.
  - Click, Enter, Space or ArrowRight open it on its first item. ArrowLeft or Escape close it and return focus to its item, without closing the parent menu.
  - Hovering opens it, and moving away closes it after a moment.
  - Submenus nest, picking an item closes every level, and on narrow screens they open below their item.
- Customers example: a Columns menu of checkbox items that shows and hides table columns with `wire:model.live`.

#### Changed
- Dropdown keyboard handling works in whichever menu has focus, and counts checkbox and radio items.
- Browser behaviour tests give server-dependent steps up to 10 seconds, since the catalog's dev server answers one request at a time.

### Installing without Livewire, and up-to-date guides

#### Added
- Installer: without Livewire, it offers to start Alpine in `resources/js/app.js` (once; it leaves an existing Alpine setup alone) and reminds you to run `npm install alpinejs` unless it's already a dependency. With Livewire, it notes that Alpine comes with it. It still never runs npm.
- The published layout includes `<x-gui::toasts />`, so toasts work out of the box.

#### Changed
- Catalog guides:
  - The Introduction lists every component and full-page example straight from the catalog, so it can't fall behind, and its outdated "forms are next" status is gone.
  - Installation gains an Alpine section and a browser support section.
  - The README matches both.

### Combobox server-side search

#### Added
- Combobox `url`: searches on the server for long lists. Typing fetches `url?q=…` after a pause (`debounce`, 300 ms by default) and shows the JSON it returns: a list of `{"value", "label"}` objects (or a `{"value": "label"}` object).
  - It works with any route and needs no Livewire. In a Livewire form, `wire:model` binds the value as usual.
  - A newer search cancels an older one. The list fades and says "Searching…" (`searching-text`) while it waits, and is `aria-busy`.
  - Enter straight after typing picks the first result once it arrives. Closing the list goes back to `options`.
  - The selected option keeps its label even when it isn't among the current results.
  - The initial options are kept out of `x-data`, so a Livewire re-render can't reset the component.
- Catalog: a timezone search over PHP's 400+ timezones on the Combobox page (plain Blade) and in a Livewire form on the Livewire page, with browser tests for both.

### Date picker in Firefox

#### Fixed
- Date picker: Firefox showed two calendar icons, its own (opening Firefox's picker) and ours. Firefox can't hide its icon, so once Alpine is running the input's right padding shrinks to centre it under our button, which now has a background matching the input (including disabled). Without JavaScript, Firefox's own picker works as before.

#### Added
- Browser behaviour tests run in Firefox too (`BROWSER=firefox npm run test:browser`), in CI as well, with a check that the date picker covers Firefox's icon.

### Proving Livewire is optional

#### Added
- CI job "Without Livewire": removes Livewire, then runs the PHP tests that don't need it, the browser behaviour tests and the accessibility audit against a catalog that loads Alpine on its own.
- Browser behaviour tests (`npm run test:browser`):
  - They load every catalog page and fail on any console error or warning (Alpine reports expression errors as warnings).
  - They drive tabs, dropdown, combobox, date picker, number input, slider, textarea counter, toasts, command palette, file upload, the table's keyboard scrolling, the catalog playground and a flashed toast after a form post.
  - They run with Livewire as well.
- Catalog: without Livewire, it loads Alpine from npm `alpinejs`, hides the Livewire guide and the Customers example, and registers Livewire's service provider only when it's installed.

### Pricing example

#### Added
- Catalog: a Pricing full-page example, a marketing page with:
  - A monthly/yearly switch that swaps the prices with CSS `:has()` on its native radios, so it needs no JavaScript.
  - Three plan cards, with the popular one highlighted.
  - A feature comparison table with row headers and labelled check and dash icons, and an FAQ accordion.
  - A "Talk to sales" modal form (number input, textarea with a counter) that closes with a toast.

### Customers example

#### Added
- Catalog: a Customers full-page example, linked from the sidebar. It's a Livewire data table with:
  - Search, a status filter and a plan filter, all kept in the URL, with sortable columns and pagination.
  - Row checkboxes and a select-all checkbox that shows a mixed state, plus a bar of bulk actions (mark churned, delete) for the selection, which is announced to screen readers.
  - A menu of actions on each row. Deleting asks for confirmation in a modal, then moves focus to the page heading.
  - An empty state that clears the filters.
- Icon: `ellipsis-horizontal`.

#### Fixed
- Table: sr-only text inside a table that scrolls sideways, such as a visually hidden column heading, no longer widens the page on small screens. The scroller is now `relative`, so it clips absolutely positioned content.
- Catalog: toasts on example pages no longer cover the "Back to the catalog" link.

### Register and Forgot password examples

#### Added
- Catalog: two more full-page examples, linked from Sign in.
  - Register: name, email, password and confirmation (each with a Show button) and a terms checkbox. It validates on the server, counts the problems in a summary, keeps everything but the passwords, and lands on the dashboard with a welcome toast.
  - Forgot password: always confirms that a link was sent, without revealing whether the account exists, and keeps the email for a resend.
- Icon: `arrow-left`.

### Stepper

#### Added
- Stepper: progress through a multi-step flow, such as a signup or checkout wizard. Pass `steps` (key => label, or label, description and href) and the `current` key, or compose `stepper.step` with a `status`. Steps are complete (a check mark), current (`aria-current="step"`), upcoming, or need attention (`invalid` keys). Horizontal or vertical; numbers come from a CSS counter. Complete steps with an `href` become links back, and composed steps with a click handler become buttons. Screen readers hear "completed" and "needs attention" after the label.
- Catalog: a Livewire signup wizard on the Livewire page that validates one step at a time, lets people jump back to completed steps, moves focus to each step's heading, and re-checks every step before finishing.

#### Fixed
- Catalog: the props and slots tables can be scrolled with the keyboard on narrow screens.

### Sidebar layout and full-page examples

#### Added
- Sidebar layout: an application shell. From the `lg` breakpoint the sidebar is a sticky column with brand, navigation and footer slots; below it, a menu button opens the same navigation in a Drawer (focus trapped, Escape closes, focus returns). A header slot holds the top bar's contents.
- Sidebar navigation: `sidebar` (a labelled `nav`), `sidebar.group` (optionally headed, named without ids so it can render twice) and `sidebar.item` (icon, badge, and `aria-current="page"` when its URL is the current one, or when `current` says so).
- Table `label`: names the scrolling region for screen readers.
- Icon: `bars-3`.
- Catalog: an Examples section of full pages built only from GiraffeUI components:
  - Dashboard: sidebar layout, stats, a table of orders, goals, an invite modal and an account menu.
  - Settings: tabs for a profile form (combobox, date picker, textarea counter, file upload), notifications (switches, radio cards, a toggle group) and account (a working theme switcher and a danger zone with a confirmation modal).
  - Sign in: a real form that validates on the server, shows errors in its fields, keeps the email, and signs in to the dashboard with a toast.
  - Not found: a 404 page with search.
- Icon: `cog-6-tooth`.

#### Fixed
- Wide tables can be scrolled with the keyboard: while a table overflows its container, the scroller joins the tab order and shows a focus ring (Chrome and Firefox did this already; Safari didn't).
- Card has `min-w-0`, so a wide table in a card in a grid or flex layout scrolls inside the card instead of stretching the page.

### Toggle group, and stable ids under Livewire

#### Added
- Segmented control `multiple`: a toggle group. The options become checkboxes named `name[]` in a labelled group, each a tab stop toggled with Space. Selected options fill with the primary colour, so they're easy to tell apart. `value` and old input take arrays, `wire:model` binds an array, errors on `name.*` mark the group invalid, and `required` is left off (it would require every box).
- The Livewire demo's notification settings pick digest days with it.

#### Fixed
- Inside Livewire components, ids that GiraffeUI generates (Tabs, Accordion, Tooltip, Combobox, Date picker, Command palette and controls without a name) are now stable across renders. They were random, so every Livewire update changed them and the morph replaced those elements: a focused control lost focus, and Alpine state was reset. They now come from the Livewire component's id plus a counter. Outside Livewire they stay random.

### Number input and slider

#### Added
- Number input: a native number input with − and + buttons that call the browser's `stepUp()` and `stepDown()`, so `step`, `min` and `max` work exactly as on the input. They fire `input` and `change`, turn off at the limits, follow values set by `wire:model` or scripts, and stay out of the tab order because the arrow keys do the same. Without JavaScript it's a plain number input with the browser's spinners.
- Slider: a native range input in the theme colour. `show-value` adds an `<output>`, `prefix` and `suffix` format it and `aria-valuetext` (so screen readers hear "8 hours"), and `marks` become a `<datalist>` for tick marks.
- Icon: `minus`.
- The Livewire demo form has a team size and years of experience.

#### Fixed
- Width classes on a control (e.g. `class="max-w-xs"`) no longer leave its parts behind. Date picker's button, Combobox's icon and list, and Input's leading and trailing content are now pinned to the control itself with anchor positioning, instead of to a full-width wrapper. Catalog pages are pixel-identical to before.

### Avatar group, description list and stat

#### Added
- Avatar group: overlapping avatars with a ring in the page colour. Pass `avatars` with `max` and the rest become a "+N" count that screen readers hear as "N more", or put avatars in the slot and pass `more`. `label` makes it a labelled group.
- Description list: a real `<dl>` of terms and details, stacked on small screens and in two columns from the `sm` breakpoint. Pass `items`, or compose `description-list.item` when a detail needs markup.
- Stat: a label, a headline value, and an optional change with a description. `trend` adds an arrow and hidden "Increased by" or "Decreased by" text, so the direction never relies on colour. `invert` is for metrics where down is good.

### Radio cards and segmented control

#### Added
- Radio `variant="card"`: the whole bordered card is the label, so clicking anywhere on it picks the option. The checked, focused, invalid and disabled states show on the card. It's still a native radio, so Tab and arrow keys work as usual.
- Segmented control: a row of options where one is always picked, built from visually hidden native radios, with no JavaScript. Tab reaches the group, arrow keys change the choice, and it submits like any radio group. It's a labelled `radiogroup` with sizes and a full-width option. `wire:model`, `x-model`, `disabled`, `required` and `form` go on every radio.
- The catalog's Livewire users table filters by role with a segmented control and `wire:model.live`.

### Date picker

#### Added
- Date picker: a native `<input type="date">` with a calendar button. The input works without JavaScript, can be typed into, validates `min` and `max`, and submits `Y-m-d`. With Alpine, the button opens a calendar in a native popover, following the WAI-ARIA date picker dialog pattern: arrow keys move by day and week, Home and End go to the start and end of the week, Page Up and Page Down change the month (with Shift, the year), and Enter or Space picks. Picking fires `input` and `change`, so `wire:model` and `x-model` work. Days outside `min` and `max` are crossed out and can't be picked. Month and day names and the first day of the week follow the page's language, or `locale` and `week-start`, and the button labels can be translated.
- Icons: `calendar` and `chevron-left`.
- The Livewire demo form has a birthday, validated on the server.

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
