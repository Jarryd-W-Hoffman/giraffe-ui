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

it('aligns columns from an align map, or per heading and cell', function () {
    $data = (string) $this->blade('<x-gui::table :columns="[\'plan\', \'price\']" :rows="[[\'plan\' => \'Pro\', \'price\' => \'$99\']]" :align="[\'price\' => \'right\']" />');
    $composed = (string) $this->blade('<x-gui::table><x-slot:head><x-gui::table.heading align="center">Seats</x-gui::table.heading></x-slot:head><x-gui::table.row><x-gui::table.cell align="center">3</x-gui::table.cell></x-gui::table.row></x-gui::table>');

    expect($data)->toMatch('/<th[^>]*class="[^"]*text-end[^"]*"[^>]*>\s*Price/')
        ->toMatch('/<td class="[^"]*text-end[^"]*">\$99<\/td>/')
        ->toMatch('/<th[^>]*class="[^"]*text-start[^"]*"[^>]*>\s*Plan/')
        // An aligned heading drops the default text-start rather than fighting it.
        ->not->toMatch('/<th[^>]*class="[^"]*text-start[^"]*text-end/')
        ->and($composed)->toMatch('/<th[^>]*text-center[^>]*>\s*Seats/')->toMatch('/<td class="[^"]*text-center[^"]*">3/');
});

it('scrolls under a sticky header with max-height', function () {
    $html = (string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" max-height="20rem" />');

    expect($html)->toContain('style="max-height: 20rem"', 'sticky top-0', 'scrollHeight > $el.clientHeight')
        ->and((string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" />'))->not->toContain('sticky top-0', 'max-height');
});

it('renders row selection as real checkboxes the header follows', function () {
    $html = (string) $this->blade('<x-gui::table><x-slot:head><x-gui::table.select-all label="Select all people" /></x-slot:head><x-gui::table.row><x-gui::table.select name="selected[]" value="7" label="Select Ada" wire:model="selected" /></x-gui::table.row></x-gui::table>');

    expect($html)
        ->toMatch('/<input\s+type="checkbox"\s+x-ref="box"\s+aria-label="Select all people"/s')
        ->toMatch('/<input\s+type="checkbox"\s+data-gui-select\s+aria-label="Select Ada"[^>]*name="selected\[\]"[^>]*value="7"[^>]*wire:model="selected"/s')
        ->toContain('has-[[data-gui-select]:checked]:bg-gui-primary/5', "new CustomEvent('gui-select-sync'")
        ->not->toContain('$wire');
});
