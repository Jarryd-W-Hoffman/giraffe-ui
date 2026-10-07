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

describe('with blade-icons', function () {
    it('renders any blade-icons icon with the same sizes and accessibility', function () {
        $html = (string) $this->blade('<x-gui::icon name="heroicon-o-bell" size="sm" class="text-gui-primary-text" data-test="x" />');

        expect($html)->toContain('<svg', 'size-4', 'shrink-0', 'text-gui-primary-text', 'data-test="x"', 'aria-hidden="true"')
            ->not->toContain('role="img"');
    });

    it('is announced when labelled', function () {
        $html = (string) $this->blade('<x-gui::icon name="heroicon-s-bell" label="Notifications" />');

        expect($html)->toContain('role="img"', 'aria-label="Notifications"', 'size-5')->not->toContain('aria-hidden');
    });

    it('still uses the bundled icon when the name is one of its own', function () {
        expect((string) $this->blade('<x-gui::icon name="check" />'))->toContain('d="'.Icons::path('check').'"');
    });

    it('lets blade-icons report an icon it does not have', function () {
        expect(fn () => (string) $this->blade('<x-gui::icon name="heroicon-o-not-a-real-icon" />'))->toThrow(ViewException::class);
    });
})->group('blade-icons')->skip(fn () => ! function_exists('svg'), 'blade-icons is not installed');

it('has exactly one aria-hidden on a decorative blade-icons icon, and none on a labelled one', function () {
    $decorative = (string) $this->blade('<x-gui::icon name="heroicon-o-bell" />');
    $labelled = (string) $this->blade('<x-gui::icon name="heroicon-o-bell" label="Bell" />');

    expect(substr_count($decorative, 'aria-hidden'))->toBe(1)
        ->and($decorative)->toMatch('/^<svg aria-hidden="true"/')
        ->and($labelled)->not->toContain('aria-hidden');
})->group('blade-icons')->skip(fn () => ! function_exists('svg'), 'blade-icons is not installed');

it('draws every bundled icon, with each shape\'s own fill rule', function () {
    foreach (array_keys(Icons::PATHS) as $name) {
        expect((string) $this->blade('<x-gui::icon :name="$name" />', ['name' => $name]))->toContain('<path');
    }

    // Some icons mix fill rules across their shapes; each shape keeps its own.
    expect(array_diff(array_keys(Icons::SHAPES), array_keys(Icons::PATHS)))->toBe([])
        ->and(count(Icons::PATHS))->toBeGreaterThan(150)
        ->and((string) $this->blade('<x-gui::icon name="briefcase" />'))->toMatch('/<path\s+fill-rule="evenodd"[^>]*d="/')->toMatch('/<path\s+d="/');
});
