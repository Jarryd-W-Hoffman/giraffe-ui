<?php

it('is a labelled region with a message, icon and action', function () {
    $html = (string) $this->blade('<x-gui::banner icon="information-circle">We launch Tuesday.<x-slot:action><a href="/news">Read more</a></x-slot:action></x-gui::banner>');

    expect($html)->toContain('<section', 'aria-label="Announcement"', 'bg-gui-primary text-gui-primary-foreground', 'We launch Tuesday.', 'href="/news"', '<svg')
        ->not->toContain('data-dismiss', 'localStorage');
});

it('has soft colours', function () {
    expect((string) $this->blade('<x-gui::banner variant="soft" color="warning">Heads up</x-gui::banner>'))->toContain('bg-gui-warning/10');
});

it('can be dismissed, remembered before paint, and reported', function () {
    $html = (string) $this->blade('<x-gui::banner dismissible remember="trial">Trial ends soon.</x-gui::banner>');

    expect($html)
        ->toContain('data-dismiss', 'aria-label="Dismiss"', "\$dispatch('banner-dismissed', { key: 'trial' })", "localStorage.setItem('gui-banner:trial', '1')")
        ->toMatch("/<\/section>\s*<script>try \{ if \(localStorage.getItem\('gui-banner:trial'\)\) document.currentScript.previousElementSibling.hidden = true; \}/");
});

it('dismisses without remembering when there is no key', function () {
    $html = (string) $this->blade('<x-gui::banner dismissible>Hi</x-gui::banner>');

    expect($html)->toContain('data-dismiss')->not->toContain('<script>');
});
