<?php

describe('breadcrumbs', function () {
    it('renders a labelled list with the current page marked', function () {
        $html = (string) $this->blade('<x-gui::breadcrumbs :items="[\'Home\' => \'/\', \'Projects\' => \'/projects\', \'GiraffeUI\' => null]" />');

        expect($html)->toContain('<nav aria-label="Breadcrumb"', '<ol')
            ->toMatch('/<a href="\/"[^>]*>Home<\/a>/')
            ->toMatch('/<a href="\/projects"[^>]*>Projects<\/a>/')
            ->toMatch('/<span aria-current="page"[^>]*>GiraffeUI<\/span>/')
            ->and(substr_count($html, '<li'))->toBe(3)
            ->and(substr_count($html, 'aria-current'))->toBe(1);
    });

    it('can be composed, with icons', function () {
        $html = (string) $this->blade('<x-gui::breadcrumbs><x-gui::breadcrumbs.item href="/"><svg data-home></svg> Home</x-gui::breadcrumbs.item><x-gui::breadcrumbs.item>Settings</x-gui::breadcrumbs.item></x-gui::breadcrumbs>');

        expect($html)->toContain('data-home')->toMatch('/aria-current="page"[^>]*>Settings/');
    });

    it('hides separators from screen readers and on the first item', function () {
        $html = (string) $this->blade('<x-gui::breadcrumbs :items="[\'A\' => \'/a\', \'B\' => null]" />');

        expect(substr_count($html, 'group-first/crumb:hidden'))->toBe(2)
            ->and($html)->toContain('aria-hidden="true"');
    });

    it('escapes labels', function () {
        expect((string) $this->blade('<x-gui::breadcrumbs :items="[\'<b>x</b>\' => null]" />'))->not->toContain('<b>x</b>');
    });
});

describe('skeleton', function () {
    it('is hidden from screen readers and sized by classes', function () {
        $html = (string) $this->blade('<x-gui::skeleton class="h-4 w-48" />');

        expect($html)->toContain('aria-hidden="true"', 'animate-pulse', 'h-4 w-48', 'motion-reduce:animate-none');
    });

    it('renders text lines with a shorter last line', function () {
        $html = (string) $this->blade('<x-gui::skeleton :lines="3" />');

        expect(substr_count($html, 'animate-pulse'))->toBe(3)
            ->and(substr_count($html, 'w-2/3'))->toBe(1);
    });
});

describe('empty state', function () {
    it('renders a title, description, icon and actions', function () {
        $html = (string) $this->blade('<x-gui::empty-state title="No projects yet" description="Create one to get started."><x-slot:icon><svg data-icon></svg></x-slot:icon> <x-slot:actions><a href="/new">New project</a></x-slot:actions></x-gui::empty-state>');

        expect($html)->toContain('<h3', 'No projects yet', 'Create one to get started.', 'data-icon', '<a href="/new">New project</a>');
    });
});
