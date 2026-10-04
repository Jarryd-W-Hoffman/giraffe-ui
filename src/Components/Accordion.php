<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use JayAitch\GiraffeUI\Support\Id;

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
        $this->guiAccordion = ['name' => $exclusive ? ($name ?? Id::make('gui-accordion')) : null];
    }

    public function render(): View
    {
        return view('giraffeui::accordion');
    }
}
