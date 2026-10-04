<?php

use Illuminate\View\ViewException;
use JayAitch\GiraffeUI\Support\Icons;

it('renders a bundled icon, decorative by default', function () {
    $html = (string) $this->blade('<x-gui::icon name="check" />');

    expect($html)->toContain('viewBox="0 0 20 20"', 'aria-hidden="true"', 'size-5', 'd="'.Icons::path('check').'"')
        ->not->toContain('role="img"');
});

it('is announced when labelled', function () {
    $html = (string) $this->blade('<x-gui::icon name="exclamation-triangle" label="Warning" />');

    expect($html)->toContain('role="img"', 'aria-label="Warning"')->not->toContain('aria-hidden');
});

it('has sizes and passes attributes through', function () {
    $html = (string) $this->blade('<x-gui::icon name="home" size="sm" class="text-gui-primary-text" data-test="x" />');

    expect($html)->toContain('size-4', 'text-gui-primary-text', 'data-test="x"');
});

it('explains what to do for an unknown icon without blade-icons', function () {
    expect(fn () => (string) $this->blade('<x-gui::icon name="rocket" />'))
        ->toThrow(ViewException::class, "GiraffeUI doesn't include an icon named [rocket]. Install blade-ui-kit/blade-icons");
})->skip(fn () => function_exists('svg'), 'blade-icons is installed');

it('bundles every icon the components use', function () {
    foreach (['chevron-down', 'chevron-right', 'chevron-up-down', 'check', 'x-mark', 'arrow-up', 'arrow-down', 'sort', 'information-circle', 'check-circle', 'exclamation-triangle', 'x-circle'] as $name) {
        expect(Icons::has($name))->toBeTrue("Missing [{$name}]");
    }
});
