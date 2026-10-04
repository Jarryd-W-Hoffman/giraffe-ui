<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Workbench\App\Catalog;

class CatalogController
{
    /**
     * Show a full-page example on its own, without the catalog around it.
     */
    public function example(string $example): View
    {
        $title = Catalog::examples()[$example] ?? abort(404);

        return view("catalog::examples.{$example}", Catalog::data() + ['title' => $title]);
    }

    /**
     * Show a guide or component page.
     */
    public function show(string $page): View
    {
        $nav = $this->nav();
        $index = array_search($page, array_column($nav, 'slug'));

        abort_if($index === false, 404);

        $shared = [
            'nav' => $nav,
            'current' => $page,
            'previous' => $nav[$index - 1] ?? null,
            'next' => $nav[$index + 1] ?? null,
        ];

        $component = Catalog::components()[$page] ?? null;

        if (! $component) {
            return view("catalog::pages.{$page}", $shared + ['title' => Catalog::guides()[$page]]);
        }

        $examples = array_map(fn (array $example) => $example + [
            'id' => 'example-'.Str::slug($example['title']),
            'html' => Blade::render($example['code'], Catalog::data()),
        ], $component['examples']);

        $playground = isset($component['playground'])
            ? $component['playground'] + ['html' => $this->renderPlayground($component['playground'], [])]
            : null;

        return view('catalog::component', $shared + [
            'slug' => $page,
            'title' => $component['name'],
            'component' => $component,
            'examples' => $examples,
            'playground' => $playground,
        ]);
    }

    /**
     * Render a component's playground with the given control values.
     */
    public function preview(Request $request, string $component): Response
    {
        $playground = Catalog::components()[$component]['playground'] ?? abort(404);

        return response($this->renderPlayground($playground, $request->query()));
    }

    /**
     * Serve the catalog's compiled CSS.
     */
    public function css(): Response
    {
        $path = __DIR__.'/../../../dist/catalog.css';

        abort_unless(is_file($path), 404, 'Run "npm run catalog:css" first.');

        return response(file_get_contents($path), 200, ['Content-Type' => 'text/css']);
    }

    /**
     * Render a playground, accepting only values its controls allow.
     *
     * @param  array<string, mixed>  $playground
     * @param  array<string, mixed>  $input
     */
    protected function renderPlayground(array $playground, array $input): string
    {
        $data = [];

        foreach ($playground['controls'] as $control) {
            $value = $input[$control['name']] ?? null;

            $data[$control['name']] = match ($control['type']) {
                'boolean' => filter_var($value ?? $control['default'], FILTER_VALIDATE_BOOLEAN),
                'select' => in_array($value, $control['options'], true) ? $value : $control['default'],
                'text' => is_string($value) ? Str::limit($value, 100, '') : $control['default'],
            };
        }

        return Blade::render($playground['template'], $data);
    }

    /**
     * Every page in sidebar order, grouped.
     *
     * @return list<array{slug: string, title: string, group: string}>
     */
    protected function nav(): array
    {
        $guides = collect(Catalog::guides())
            ->map(fn ($title, $slug) => ['slug' => $slug, 'title' => $title, 'group' => 'Getting started']);

        $components = collect(Catalog::components())
            ->map(fn ($component, $slug) => ['slug' => $slug, 'title' => $component['name'], 'group' => $component['group'] ?? 'Components']);

        $examples = collect(Catalog::examples())
            ->map(fn ($title, $slug) => ['slug' => "examples/{$slug}", 'title' => $title, 'group' => 'Examples']);

        return $guides->merge($components)->merge($examples)->values()->all();
    }
}
