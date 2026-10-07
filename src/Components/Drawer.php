<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;

/**
 * A modal that slides in from the side of the screen. Same <dialog>, same behaviour, same modal.close.
 */
class Drawer extends Modal
{
    public function __construct(
        string $id,
        ?string $title = null,
        ?string $description = null,
        string $size = 'md',
        bool $dismissible = true,
        bool $open = false,
        public string $side = 'right',
    ) {
        parent::__construct($id, $title, $description, $size, $dismissible, $open);

        $this->side = $side === 'left' ? 'left' : 'right';
    }

    public function render(): View
    {
        return view('giraffeui::modal');
    }
}
