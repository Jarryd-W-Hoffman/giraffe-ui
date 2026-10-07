<?php

// Every piece of text GiraffeUI shows or announces on its own. Publish with
// `php artisan vendor:publish --tag=giraffeui-lang` and translate lang/vendor/giraffeui/{locale}/ui.php.
// Props such as button-label still win over these when you pass them.

return [
    // Overlays and layout
    'close' => 'Close',
    'dismiss' => 'Dismiss',
    'announcement' => 'Announcement',
    'navigation' => 'Navigation',
    'menu' => 'Menu',
    'open_navigation' => 'Open navigation',
    'collapse_sidebar' => 'Collapse sidebar',
    'expand_sidebar' => 'Expand sidebar',
    'main' => 'Main',
    'breadcrumb' => 'Breadcrumb',
    'command_palette' => 'Command palette',
    'search' => 'Search…',

    // Results and loading
    'no_results' => 'No results',
    'no_results_table' => 'No results.',
    'searching' => 'Searching…',

    // Combobox with multiple. :label is the option's label.
    'remove' => 'Remove :label',
    'added' => ':label added',
    'removed' => ':label removed',

    // Pagination. :first, :last and :total are numbers.
    'pagination' => 'Pagination',
    'showing' => 'Showing :first to :last of :total',
    'previous' => 'Previous',
    'next' => 'Next',

    // Stepper
    'progress' => 'Progress',
    'step_completed' => 'completed',
    'step_needs_attention' => 'needs attention',

    // Stat
    'increased_by' => 'Increased by',
    'decreased_by' => 'Decreased by',

    // Input
    'show_password' => 'Show password',

    // Copy button
    'copy' => 'Copy',
    'copied' => 'Copied',
    'copy_failed' => 'Couldn\'t copy',

    // Number input
    'increase' => 'Increase',
    'decrease' => 'Decrease',

    // Table selection
    'select_row' => 'Select row',
    'select_all_rows' => 'Select all rows',

    // Date picker
    'choose_date' => 'Choose date',
    'choose_dates' => 'Choose dates',
    'choose_time' => 'Choose time',
    'date' => 'Date',
    'time' => 'Time',
    'previous_month' => 'Previous month',
    'next_month' => 'Next month',
    'start_date' => 'Start date',
    'end_date' => 'End date',
    'pick_start' => 'Pick the start date',
    'pick_end' => 'Pick the end date',
    'date_unavailable' => "That date isn't available.",

    // Textarea counter. :count is a number.
    'characters' => ':count characters',
    'character' => ':count character',
    'characters_left' => ':count characters left',
    'character_left' => ':count character left',
    'up_to_characters' => 'Up to :max characters.',

    // File upload. :choose is the highlighted "Choose…" text.
    'choose_file' => 'Choose a file',
    'choose_files' => 'Choose files',
    'drop_file' => ':choose or drag it here',
    'drop_files' => ':choose or drag them here',
    'chosen_files' => 'Chosen files',
    'uploading' => 'Uploading',
    'upload_failed' => 'The upload failed. Please try again.',

    // Rating. :count, :value and :max are numbers.
    'star' => ':count star',
    'stars' => ':count stars',
    'rated' => 'Rated :value out of :max',

    // Theme switcher
    'theme' => 'Theme',
    'light' => 'Light',
    'dark' => 'Dark',
    'system' => 'System',
    'dark_mode' => 'Dark mode',

    // Charts. Values are already formatted.
    'trend' => 'Trend from :first to :last, high :high, low :low',
    'chart_summary' => ':series from :from to :to',
    'show_data' => 'Show the data',
    'point' => 'Point',
    'total' => 'Total',
    'other' => 'Other',
    'share' => ':value, :percent of the total',
    'series' => 'Series',
    'scatter_summary' => ':y by :x',
    'heatmap_summary' => 'Heatmap, :rows rows by :columns columns',
    'no_data' => 'No data yet.',

    // Avatar group. :count is a number.
    'more' => ':count more',
];
