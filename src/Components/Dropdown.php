<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * A menu of actions in a native popover.
 *
 * A class so its items can close the menu through a uniquely named @aware key.
 */
class Dropdown extends Component
{
    /**
     * @var array{id: string}
     */
    public array $guiDropdown;

    public function __construct(
        public string $id,
        public ?string $label = null,
        public string $position = 'bottom-start',
    ) {
        $this->guiDropdown = ['id' => $id];
    }

    public function render(): View
    {
        return view('giraffeui::dropdown');
    }
}
