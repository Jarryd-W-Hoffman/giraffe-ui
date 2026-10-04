<?php

beforeEach(function () {
    $this->html = (string) $this->blade(<<<'BLADE'
        <x-gui::command-palette id="cmd" placeholder="Search pages">
            <x-gui::command-palette.group heading="Pages">
                <x-gui::command-palette.item href="/dashboard" icon="home" keywords="overview stats">Dashboard</x-gui::command-palette.item>
                <x-gui::command-palette.item href="/settings" shortcut="⌘ ,">Settings</x-gui::command-palette.item>
            </x-gui::command-palette.group>
            <x-gui::command-palette.group heading="Actions">
                <x-gui::command-palette.item x-on:click="GiraffeUI.setTheme('dark')">Dark mode</x-gui::command-palette.item>
                <x-gui::command-palette.item wire:click="logout">Sign out</x-gui::command-palette.item>
            </x-gui::command-palette.group>
        </x-gui::command-palette>
        BLADE);
});

it('is a labelled dialog with a combobox controlling a listbox', function () {
    expect($this->html)
        ->toMatch('/<dialog\s+id="cmd"\s+aria-label="Command palette"\s+closedby="any"/')
        ->toMatch('/<input(?=[^>]*role="combobox")(?=[^>]*aria-controls="cmd-listbox")(?=[^>]*aria-label="Search pages")[^>]*>/s')
        ->toContain('id="cmd-listbox" role="listbox"');
});

it('opens with the shortcut', function () {
    expect($this->html)->toContain('x-on:keydown.window.meta.k.prevent', 'x-on:keydown.window.ctrl.k.prevent');
});

it('groups options under labelled headings', function () {
    expect(substr_count($this->html, 'role="group"'))->toBe(2)
        ->and(substr_count($this->html, 'role="option"'))->toBe(4)
        ->and($this->html)->toMatch('/role="group" aria-labelledby="(gui-cmd-group-[a-z0-9]+)".*?id="\1"[^>]*>Pages</s');
});

it('renders links and buttons, keeping focus in the search box', function () {
    expect($this->html)
        ->toMatch('/<a(?=[^>]*role="option")(?=[^>]*tabindex="-1")(?=[^>]*href="\/dashboard")[^>]*>/s')
        ->toMatch('/<button(?=[^>]*role="option")(?=[^>]*type="button")(?=[^>]*wire:click="logout")[^>]*>/s');
});

it('searches text and keywords', function () {
    expect($this->html)->toContain('data-search="dashboard overview stats"', 'data-search="settings"');
});

it('closes the palette before running an item\'s own click handler', function () {
    expect(html_entity_decode($this->html, ENT_QUOTES))->toContain("x-on:click=\"\$el.closest('dialog')?.close(); GiraffeUI.setTheme('dark')\"")
        ->and(substr_count($this->html, 'GiraffeUI.setTheme'))->toBe(1);
});

it('shows shortcuts and icons', function () {
    expect($this->html)->toContain('<kbd', '>⌘</kbd>', '>,</kbd>', 'viewBox="0 0 20 20"');
});

it('can turn the shortcut off', function () {
    expect((string) $this->blade('<x-gui::command-palette :shortcut="null" />'))->not->toContain('keydown.window');
});
