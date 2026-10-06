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
        public string $side = 'end',
    ) {
        parent::__construct($id, $title, $description, $size, $dismissible, $open);

        // start and end follow the reading direction; left and right are fixed.
        $this->side = in_array($side, ['start', 'end', 'left', 'right'], true) ? $side : 'end';
    }

    public function render(): View
    {
        return view('giraffeui::modal');
    }
}
