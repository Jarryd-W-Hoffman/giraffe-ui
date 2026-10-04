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

it('has no duplicate ids, so examples can target their own elements', function (string $page) {
    $dom = new DOMDocument;
    @$dom->loadHTML($this->get("/catalog/{$page}")->getContent(), LIBXML_NOERROR);

    $ids = array_map(fn (DOMElement $element) => $element->getAttribute('id'), iterator_to_array((new DOMXPath($dom))->query('//*[@id]')));

    expect(array_keys(array_filter(array_count_values($ids), fn ($count) => $count > 1)))->toBe([]);
})->with(fn () => [...array_keys(Catalog::guides()), ...array_keys(Catalog::components()), ...array_map(fn ($slug) => "examples/{$slug}", array_keys(Catalog::examples()))]);

it('returns 404 for unknown pages', function () {
    $this->get('/catalog/nope')->assertNotFound();
    $this->get('/catalog/examples/nope')->assertNotFound();
});

it('renders the full-page examples on their own, linked from the sidebar', function (string $example) {
    $this->get("/catalog/examples/{$example}")
        ->assertOk()
        ->assertDontSee('id="sidebar"', false)
        ->assertSee('Back to the catalog');

    $this->get('/catalog/introduction')->assertSee("href=\"/catalog/examples/{$example}\"", false);
})->with(fn () => array_keys(Catalog::examples()));

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
        ->assertSee('border-gui-danger-text', false)
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
