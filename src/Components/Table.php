<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * A table, either built from data (columns + rows) or composed from table.heading, table.row and table.cell.
 *
 * A class so composed headings can read the current sort through a uniquely named @aware key.
 */
class Table extends Component
{
    /**
     * @var array{sort: ?string, direction: string, align: array<string, string>}
     */
    public array $guiTable;

    /**
     * @var Collection<string, string>|null
     */
    public ?Collection $headings;

    /**
     * @param  array<int|string, string>|null  $columns  ['name', 'email'] or ['name' => 'Full name']
     * @param  iterable<mixed>  $rows
     * @param  array<int, string>  $sortable  Column keys that can be sorted.
     * @param  string|null  $label  Names the scrolling region for screen readers.
     * @param  array<string, string>  $align  Column key => 'left' | 'center' | 'right', for tables built from columns.
     * @param  string|null  $maxHeight  A CSS length. The table scrolls within it, with the header stuck to the top.
     */
    public function __construct(
        ?array $columns = null,
        public iterable $rows = [],
        public array $sortable = [],
        public ?string $sort = null,
        public string $direction = 'asc',
        public ?string $label = null,
        public array $align = [],
        public ?string $maxHeight = null,
    ) {
        $this->direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $this->guiTable = ['sort' => $sort, 'direction' => $this->direction, 'align' => $align];

        $this->headings = $columns === null ? null : collect($columns)->mapWithKeys(fn ($label, $key) => is_int($key)
            ? [$label => Str::headline($label)]
            : [$key => $label]
        );
    }

    public function render(): View
    {
        return view('giraffeui::table');
    }
}
