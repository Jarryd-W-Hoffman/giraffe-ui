<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * A group of <details> elements. With exclusive, they share a name so the browser keeps only one open.
 */
class Accordion extends Component
{
    /**
     * @var array{name: ?string}
     */
    public array $guiAccordion;

    public function __construct(public bool $exclusive = false, ?string $name = null)
    {
        $this->guiAccordion = ['name' => $exclusive ? ($name ?? 'gui-accordion-'.Str::lower(Str::random(8))) : null];
    }

    public function render(): View
    {
        return view('giraffeui::accordion');
    }
}
