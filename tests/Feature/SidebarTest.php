<?php

it('renders a labelled nav with groups and links', function () {
    $html = (string) $this->blade(<<<'BLADE'
        <x-gui::sidebar label="Main">
            <x-gui::sidebar.group heading="Help">
                <x-gui::sidebar.item href="/docs" icon="home" badge="3">Docs</x-gui::sidebar.item>
            </x-gui::sidebar.group>
        </x-gui::sidebar>
        BLADE);

    expect(attr($html, 'nav', 'aria-label'))->toBe('Main')
        ->and($html)->toMatch('/<div\s+role="group"\s+aria-label="Help"/')
        ->toMatch('/<ul[^>]*>\s*<li>\s*<a\s+href="\/docs"/')
        ->toContain('>3</span>')
        ->not->toMatch('/\sid="/');
});

it('marks the current page automatically, or when told', function () {
    $this->get('/');

    $auto = (string) $this->blade('<x-gui::sidebar.item href="'.url('/').'">Home</x-gui::sidebar.item><x-gui::sidebar.item href="/elsewhere">Else</x-gui::sidebar.item>');
    $forced = (string) $this->blade('<x-gui::sidebar.item href="/x" :current="true">X</x-gui::sidebar.item>');
    $off = (string) $this->blade('<x-gui::sidebar.item href="'.url('/').'" :current="false">Home</x-gui::sidebar.item>');

    expect(substr_count($auto, 'aria-current="page"'))->toBe(1)
        ->and($auto)->toMatch('/aria-current="page"[^>]*>.*?<span class="min-w-0[^"]*">Home/s')
        ->and($forced)->toContain('aria-current="page"')
        ->and($off)->not->toContain('aria-current');
});

it('renders the navigation in a column and in a drawer opened by the menu button', function () {
    $html = (string) $this->blade(<<<'BLADE'
        <x-gui::sidebar-layout menu-label="Menu">
            <x-slot:brand>Acme</x-slot:brand>
            <x-slot:sidebar><x-gui::sidebar><x-gui::sidebar.group><x-gui::sidebar.item href="/a">A</x-gui::sidebar.item></x-gui::sidebar.group></x-gui::sidebar></x-slot:sidebar>
            <x-slot:header><span>Search</span></x-slot:header>
            <p>Page</p>
        </x-gui::sidebar-layout>
        BLADE);

    expect(substr_count($html, '<nav aria-label="Main"'))->toBe(2)
        ->and($html)->toMatch('/<aside\s[^>]*class="hidden[^"]*lg:flex/s')
        ->toMatch('/<button(?=[^>]*commandfor="gui-sidebar-drawer")(?=[^>]*command="show-modal")(?=[^>]*aria-label="Menu")[^>]*>/s')
        ->toMatch('/<dialog[^>]*id="gui-sidebar-drawer"/')
        ->toContain('<main class="flex-1', '<p>Page</p>', '<span>Search</span>')
        ->and(classesOf($html, 'header'))->not->toContain('lg:hidden');
});

it('hides the top bar on large screens when there is no header', function () {
    $html = (string) $this->blade('<x-gui::sidebar-layout><x-slot:sidebar>Nav</x-slot:sidebar> Page</x-gui::sidebar-layout>');

    expect(classesOf($html, 'header'))->toContain('lg:hidden');
});

it('collapses to a rail when collapsible, remembering the choice before paint', function () {
    $html = (string) $this->blade('<x-gui::sidebar-layout collapsible collapsed><x-slot:sidebar><x-gui::sidebar><x-gui::sidebar.group><x-gui::sidebar.item href="/a" icon="home">Home</x-gui::sidebar.item><x-gui::sidebar.item href="/b">Billing</x-gui::sidebar.item></x-gui::sidebar.group></x-gui::sidebar></x-slot:sidebar> Page</x-gui::sidebar-layout>');

    expect($html)
        ->toMatch('/data-sidebar-layout\s+data-collapsed/')
        ->toContain('lg:data-collapsed:grid-cols-[4rem_minmax(0,1fr)]', "localStorage.getItem('gui-sidebar-collapsed')")
        ->toContain('Collapse sidebar', 'Expand sidebar', 'x-on:gui-sidebar-expand="set(false)"')
        // Labels stay for screen readers in the rail; an item without an icon shows its first letter.
        ->toContain('in-data-collapsed:sr-only">Home</span>', '>B</span>', 'position-anchor: --gui-gui-sidebar-item');
});

it('leaves the sidebar alone unless it is collapsible', function () {
    expect((string) $this->blade('<x-gui::sidebar-layout><x-slot:sidebar>Nav</x-slot:sidebar> Page</x-gui::sidebar-layout>'))
        ->not->toContain('data-collapsed', 'localStorage', 'Collapse sidebar');
});

it('nests items in a submenu that opens when it holds the current page', function () {
    $closed = (string) $this->blade('<x-gui::sidebar.submenu label="Projects" icon="folder" badge="3"><x-gui::sidebar.item href="/x">Website</x-gui::sidebar.item></x-gui::sidebar.submenu>');
    $open = (string) $this->blade('<x-gui::sidebar.submenu label="Projects"><x-gui::sidebar.item href="/x" :current="true">Website</x-gui::sidebar.item></x-gui::sidebar.submenu>');

    expect($closed)->toMatch('/<details\s+class="group\/submenu">/')->toContain('<summary', 'Projects', '>3<', 'Website')
        ->and($open)->toMatch('/<details\s+open\s+class="group\/submenu">/')->toContain('text-gui-primary-text', '>P</span>');
});

it('lets a group heading show and hide the group', function () {
    $html = (string) $this->blade('<x-gui::sidebar.group heading="Help" collapsible :open="false"><x-gui::sidebar.item href="/docs">Docs</x-gui::sidebar.item></x-gui::sidebar.group>');

    expect($html)->toMatch('/<details\s+class="group\/section">/')->toContain('<summary', 'Help')->not->toContain('role="group"')
        ->and((string) $this->blade('<x-gui::sidebar.group heading="Help" collapsible :open="false"><x-gui::sidebar.item href="/docs" :current="true">Docs</x-gui::sidebar.item></x-gui::sidebar.group>'))
        ->toMatch('/<details\s+open\s+class="group\/section">/');
});
