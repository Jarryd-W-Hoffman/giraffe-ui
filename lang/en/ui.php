<?php

// Every piece of text GiraffeUI shows or announces on its own. Publish with
// `php artisan vendor:publish --tag=giraffeui-lang` and translate lang/vendor/giraffeui/{locale}/ui.php.
// Props such as button-label still win over these when you pass them.

return [
    // Overlays and layout
    'close' => 'Close',
    'dismiss' => 'Dismiss',
    'navigation' => 'Navigation',
    'open_navigation' => 'Open navigation',
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

    // Number input
    'increase' => 'Increase',
    'decrease' => 'Decrease',

    // Table selection
    'select_row' => 'Select row',
    'select_all_rows' => 'Select all rows',

    // Date picker
    'choose_date' => 'Choose date',
    'choose_dates' => 'Choose dates',
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

    // Avatar group. :count is a number.
    'more' => ':count more',
];
