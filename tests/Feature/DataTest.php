<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use JayAitch\GiraffeUI\Support\Slot;

function people(): array
{
    return [
        ['name' => 'Ada', 'email' => 'ada@example.com'],
        ['name' => 'Grace', 'email' => 'grace@example.com'],
    ];
}

function paginator(int $page = 2, int $total = 50, int $perPage = 10): LengthAwarePaginator
{
    return new LengthAwarePaginator(range(1, $perPage), $total, $perPage, $page, ['path' => '/users']);
}

describe('sortable table', function () {
    it('links sortable headings and marks the sorted column', function () {
        $this->get('/?sort=name&direction=asc');

        $html = (string) $this->blade('<x-gui::table :columns="[\'name\', \'email\']" :rows="$rows" :sortable="[\'name\', \'email\']" sort="name" direction="asc" />', ['rows' => people()]);

        expect($html)
            ->toMatch('/<th[^>]*aria-sort="ascending"[^>]*>\s*<a href="[^"]*sort=name&amp;direction=desc/s')
            ->toMatch('/<a href="[^"]*sort=email&amp;direction=asc/s')
            ->and(substr_count($html, 'aria-sort'))->toBe(1);
    });

    it('marks descending sorts', function () {
        $html = (string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" :sortable="[\'name\']" sort="name" direction="DESC" />');

        expect($html)->toContain('aria-sort="descending"')->toMatch('/direction=asc/');
    });

    it('drops the page when the sort changes', function () {
        $this->get('/?page=3');

        $html = (string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]" :sortable="[\'name\']" />');

        expect($html)->not->toContain('page=3');
    });

    it('leaves unsortable columns as plain headings', function () {
        $html = (string) $this->blade('<x-gui::table :columns="[\'name\', \'email\']" :rows="[]" :sortable="[\'name\']" />');

        expect(substr_count($html, '<a href'))->toBe(1);
    });
});

describe('composed table', function () {
    beforeEach(function () {
        $this->html = (string) $this->blade(<<<'BLADE'
            <x-gui::table sort="name" direction="desc">
                <x-slot:head>
                    <x-gui::table.heading sortable="name" wire:click="sortBy('name')" class="w-1/2">Name</x-gui::table.heading>
                    <x-gui::table.heading>Email</x-gui::table.heading>
                </x-slot:head>
                @foreach ($people as $person)
                    <x-gui::table.row wire:key="{{ $person['name'] }}">
                        <x-gui::table.cell><strong>{{ $person['name'] }}</strong></x-gui::table.cell>
                        <x-gui::table.cell wrap>{{ $person['email'] }}</x-gui::table.cell>
                    </x-gui::table.row>
                @endforeach
            </x-gui::table>
            BLADE, ['people' => people()]);
    });

    it('renders custom cells', function () {
        expect($this->html)->toContain('<strong>Ada</strong>', 'grace@example.com', 'wire:key="Ada"')
            ->not->toContain('No results.');
    });

    it('puts wire:click on the sort button and other attributes on the th', function () {
        expect($this->html)
            ->toMatch('/<th(?=[^>]*aria-sort="descending")(?=[^>]*class="[^"]*w-1\/2)(?![^>]*wire:click)[^>]*>/s')
            ->toMatch('/<button type="button"[^>]*wire:click="sortBy\(\'name\'\)"/s');
    });

    it('lets cells wrap', function () {
        expect($this->html)->toMatch('/<td class="px-4 py-3">\s*grace@example.com/s');
    });

    it('shows the empty state when there are no rows', function () {
        $html = (string) $this->blade('<x-gui::table><x-slot:head><x-gui::table.heading>Name</x-gui::table.heading></x-slot:head> <x-slot:empty>Nobody yet</x-slot:empty></x-gui::table>');

        expect($html)->toContain('Nobody yet', 'colspan="100"');
    });

    it('renders a footer', function () {
        $html = (string) $this->blade('<x-gui::table :columns="[\'name\']" :rows="[]"><x-slot:footer>Footer here</x-slot:footer></x-gui::table>');

        expect($html)->toContain('Footer here');
    });
});

describe('pagination', function () {
    it('renders pages, the current page and a summary', function () {
        $html = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => paginator()]);

        expect($html)->toContain('aria-label="Pagination"', 'Showing', '>11<', '>20<', '>50<', 'aria-current="page"', 'rel="prev"', 'rel="next"', '/users?page=3')
            ->toMatch('/aria-current="page"><span class="sr-only">Page <\/span>2</');
    });

    it('disables previous on the first page and next on the last', function () {
        $first = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => paginator(page: 1)]);
        $last = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => paginator(page: 5)]);

        expect($first)->toMatch('/<span[^>]*aria-disabled="true">Previous/')->not->toContain('rel="prev"')
            ->and($last)->toMatch('/<span[^>]*aria-disabled="true">Next/')->not->toContain('rel="next"');
    });

    it('collapses long ranges', function () {
        $html = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => paginator(page: 10, total: 500)]);

        expect($html)->toContain('...');
    });

    it('works with simple paginators', function () {
        $simple = new Paginator(range(1, 11), 10, 2, ['path' => '/users']);

        $html = (string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => $simple]);

        expect($html)->toContain('rel="prev"', 'rel="next"')->not->toContain('Showing');
    });

    it('renders nothing with a single page', function () {
        expect(trim(strip_tags((string) $this->blade('<x-gui::pagination :paginator="$paginator" />', ['paginator' => paginator(page: 1, total: 5)]))))->toBe('');
    });

    it('uses Livewire actions when asked', function () {
        $html = (string) $this->blade('<x-gui::pagination :paginator="$paginator" livewire />', ['paginator' => paginator()]);

        expect($html)->toContain("wire:click=\"previousPage('page')\"", "wire:click=\"nextPage('page')\"", "wire:click=\"gotoPage(3, 'page')\"")
            ->not->toContain('href=');
    });

    it('is added to a table automatically when rows are paginated', function () {
        $html = (string) $this->blade('<x-gui::table :columns="[\'id\']" :rows="$paginator" />', ['paginator' => new LengthAwarePaginator(collect(range(11, 20))->map(fn ($id) => ['id' => $id]), 50, 10, 2, ['path' => '/users'])]);

        expect($html)->toContain('aria-label="Pagination"', '>11<');
    });
});

describe('slots', function () {
    it('treats Livewire morph markers as blank', function () {
        expect(Slot::isBlank("\n <!--[if BLOCK]><![endif]-->\n<!--[if ENDBLOCK]><![endif]-->  "))->toBeTrue()
            ->and(Slot::isBlank('<!--[if BLOCK]><![endif]--><tr></tr>'))->toBeFalse()
            ->and(Slot::isBlank(null))->toBeTrue();
    });
});
