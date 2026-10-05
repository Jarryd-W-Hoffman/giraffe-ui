<?php

it('turns column keys into headings', function () {
    $this->blade('<x-gui::table :columns="[\'first_name\', \'email\']" :rows="[]" />')
        ->assertSeeInOrder(['First Name', 'Email']);
});

it('uses custom column labels', function () {
    $this->blade('<x-gui::table :columns="[\'email\' => \'Email address\']" :rows="[]" />')
        ->assertSee('Email address');
});

it('renders values from arrays, objects and nested keys', function () {
    $rows = [
        ['name' => 'Ada', 'team' => ['name' => 'Engines']],
        (object) ['name' => 'Grace', 'team' => (object) ['name' => 'Compilers']],
    ];

    $this->blade('<x-gui::table :columns="[\'name\', \'team.name\' => \'Team\']" :rows="$rows" />', ['rows' => $rows])
        ->assertSeeInOrder(['Ada', 'Engines', 'Grace', 'Compilers']);
});

it('escapes cell values', function () {
    $this->blade('<x-gui::table :columns="[\'name\']" :rows="[[\'name\' => \'<script>x</script>\']]" />')
        ->assertDontSee('<script>', false)
        ->assertSee('&lt;script&gt;', false);
});

it('shows an empty state', function () {
    $this->blade('<x-gui::table :columns="[\'name\', \'email\']" :rows="[]" />')
        ->assertSee('No results.')
        ->assertSee('colspan="2"', false);
});

it('accepts a custom empty state', function () {
    $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]"><x-slot:empty>Nobody here yet</x-slot:empty></x-gui::table>')
        ->assertSee('Nobody here yet')
        ->assertDontSee('No results.');
});

it('merges classes onto the wrapper', function () {
    $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" class="mt-6" />')
        ->assertSee('mt-6', false);
});

it('lets keyboard users scroll a wide table, and names the region when labelled', function () {
    $plain = (string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" />');
    $labelled = (string) $this->blade('<x-gui::table label="Recent orders" :columns="[\'name\']" :rows="[]" />');

    expect($plain)->toContain('x-init="new ResizeObserver(')
        ->not->toContain('role="region"')
        ->and($labelled)->toMatch('/<div\s+class="relative overflow-x-auto[^"]*"\s+role="region" aria-label="Recent orders"/');
});

it('clips sr-only text inside the scroller, so a wide table never widens the page', function () {
    // sr-only is absolutely positioned; without a positioned scroller it escapes the overflow clip.
    expect((string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" />'))
        ->toMatch('/<div\s+class="relative overflow-x-auto/');
});
