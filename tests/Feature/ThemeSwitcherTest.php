<?php

it('offers light, dark and system as a named radio group', function () {
    $html = (string) $this->blade('<x-gui::theme-switcher />');

    expect($html)->toContain('role="radiogroup"', 'aria-label="Theme"', 'value="light"', 'value="dark"', 'value="system"', '>Light<', '>System<', 'GiraffeUI.setTheme')
        ->toMatch('/value="system"\s+class="sr-only"\s+checked/')
        ->and(substr_count($html, 'type="radio"'))->toBe(3);
});

it('names the icons when labels are hidden', function () {
    $html = (string) $this->blade('<x-gui::theme-switcher :labels="false" />');

    expect($html)->toContain('aria-label="Light"', 'aria-label="Dark"', 'aria-label="System"')->not->toContain('<span>Light</span>');
});

it('can be a single dark mode toggle', function () {
    $html = (string) $this->blade('<x-gui::theme-switcher variant="toggle" />');

    expect($html)->toContain('aria-label="Dark mode"', 'aria-pressed="false"', "set(dark ? 'light' : 'dark')")
        ->not->toContain('type="radio"');
});

it('defines setTheme if the layout did not, and follows the system theme once per page', function () {
    expect((string) $this->blade('<x-gui::theme-switcher />'))->toContain('gui.setTheme ??=', 'gui.applyTheme ??=', 'watchingSystemTheme', 'MutationObserver');
});
