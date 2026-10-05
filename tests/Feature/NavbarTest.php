<?php

it('renders a header with a named nav, links in the bar and the menu, and actions once', function () {
    $html = (string) $this->blade('<x-gui::navbar label="Site"><x-slot:brand><a href="/">Acme</a></x-slot:brand><x-gui::navbar.item href="/pricing" current>Pricing</x-gui::navbar.item><x-slot:actions><button id="account">Account</button></x-slot:actions></x-gui::navbar>');

    preg_match('/commandfor="([^"]+)"/', $html, $menu);

    expect($html)
        ->toContain('<header', 'Acme')
        ->and(substr_count($html, '<nav aria-label="Site"'))->toBe(2)
        ->and(substr_count($html, 'href="/pricing"'))->toBe(2)
        ->and(substr_count($html, 'aria-current="page"'))->toBe(2)
        ->and(substr_count($html, 'id="account"'))->toBe(1)
        ->and($html)->toMatch('/id="'.preg_quote($menu[1], '/').'"\s+popover/')
        ->toContain('command="toggle-popover"', 'aria-label="Menu"', 'data-place="bar"', 'data-place="menu"');
});

it('marks the current page from the URL', function () {
    $this->get('/');
    $html = (string) $this->blade('<x-gui::navbar><x-gui::navbar.item href="'.url('/').'">Home</x-gui::navbar.item><x-gui::navbar.item href="/elsewhere">Elsewhere</x-gui::navbar.item></x-gui::navbar>');

    expect(substr_count($html, 'aria-current="page"'))->toBe(2)
        ->and($html)->toMatch('/aria-current="page"[^>]*>Home/');
});

it('can stick to the top', function () {
    expect((string) $this->blade('<x-gui::navbar sticky />'))->toContain('sticky top-0')
        ->and((string) $this->blade('<x-gui::navbar />'))->not->toContain('sticky top-0');
});
