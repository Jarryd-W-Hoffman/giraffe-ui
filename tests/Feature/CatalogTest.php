<?php

use Illuminate\Support\Facades\Blade;
use Workbench\App\Catalog;
use Workbench\App\Providers\WorkbenchServiceProvider;

beforeEach(function () {
    $this->app->register(WorkbenchServiceProvider::class);
});

it('renders every catalog page', function (string $page) {
    $this->get("/catalog/{$page}")->assertOk();
})->with(fn () => [...array_keys(Catalog::guides()), ...array_keys(Catalog::components())]);

it('returns 404 for unknown pages', function () {
    $this->get('/catalog/nope')->assertNotFound();
});

it('renders every example exactly as its code is written', function () {
    foreach (Catalog::components() as $component) {
        foreach ($component['examples'] as $example) {
            expect(Blade::render($example['code'], Catalog::data()))->not->toBeEmpty();
        }
    }
});

it('renders the playground with the requested props', function () {
    $this->get('/catalog/preview/button?variant=outline&color=danger&label=Delete&disabled=true')
        ->assertOk()
        ->assertSee('border-gui-danger', false)
        ->assertSee('Delete')
        ->assertSee('disabled', false);
});

it('ignores playground values the controls do not allow', function () {
    $this->get('/catalog/preview/button?variant=evil&size=%22%3E&label=%3Cscript%3E')
        ->assertOk()
        ->assertSee('bg-gui-primary', false)
        ->assertSee('h-10', false)
        ->assertDontSee('<script>', false);
});
