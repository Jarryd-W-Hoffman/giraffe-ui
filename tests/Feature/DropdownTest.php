<?php

it('renders checkbox items as real checkboxes with the menuitemcheckbox role', function () {
    $html = (string) $this->blade('<x-gui::dropdown id="view"><x-gui::dropdown.checkbox name="show[]" value="drafts" checked wire:model.live="show">Drafts</x-gui::dropdown.checkbox></x-gui::dropdown>');

    expect($html)
        ->toMatch('/<input type="checkbox" role="menuitemcheckbox"[^>]*name="show\[\]"[^>]*value="drafts"[^>]*checked[^>]*wire:model.live="show"/s')
        ->toMatch('/<label[^>]*>\s*<input/s')
        // Toggling doesn't close the menu, unlike action items.
        ->not->toContain('command="hide-popover"');
});

it('renders radio items in a named group', function () {
    $html = (string) $this->blade('<x-gui::dropdown id="sort"><x-gui::dropdown.group heading="Sort by"><x-gui::dropdown.radio name="sort" value="name" checked>Name</x-gui::dropdown.radio><x-gui::dropdown.radio name="sort" value="date">Date</x-gui::dropdown.radio></x-gui::dropdown.group></x-gui::dropdown>');

    preg_match('/role="group"\s+aria-labelledby="([^"]+)"/', $html, $group);

    expect($group)->not->toBeEmpty()
        ->and($html)->toMatch('/id="'.preg_quote($group[1], '/').'"[^>]*>Sort by</')
        ->and(substr_count($html, 'role="menuitemradio"'))->toBe(2)
        // Arrow keys move through the menu rather than changing the checked radio.
        ->and($html)->toContain('x-on:keydown.left.prevent x-on:keydown.right.prevent');
});

it('nests submenus as popovers whose items close the whole menu', function () {
    $html = (string) $this->blade('<x-gui::dropdown id="file"><x-gui::dropdown.submenu label="Share"><x-gui::dropdown.item>Copy link</x-gui::dropdown.item></x-gui::dropdown.submenu></x-gui::dropdown>');

    preg_match('/aria-controls="([^"]+)"/', $html, $panel);

    expect($html)
        ->toMatch('/<button(?=[^>]*role="menuitem")(?=[^>]*aria-haspopup="menu")(?=[^>]*aria-expanded="false")[^>]*>/s')
        ->toMatch('/id="'.preg_quote($panel[1], '/').'"\s+x-ref="panel"\s+popover\s+role="menu"\s+aria-label="Share"/')
        ->toContain('[position-area:x-end_span-bottom]')
        // The item inside the submenu targets the root menu, so picking it closes every level.
        ->toMatch('/commandfor="file"\s+command="hide-popover"[^>]*>\s*Copy link/s');
});

it('scopes keyboard navigation to the menu that has focus', function () {
    expect((string) $this->blade('<x-gui::dropdown id="x"><x-gui::dropdown.item>A</x-gui::dropdown.item></x-gui::dropdown>'))
        ->toContain('[role^=menuitem]:not([aria-disabled=true]):not(:disabled)', "item.closest('[role=menu]') === menu");
});
