<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * A native <dialog>.
 *
 * A class so <x-gui::modal.close> inside it can find the dialog through a uniquely named @aware key,
 * rather than picking up the id of whichever parent component happens to be nearest.
 */
class Modal extends Component
{
    /**
     * @var array{id: string}
     */
    public array $guiModal;

    public function __construct(
        public string $id,
        public ?string $title = null,
        public ?string $description = null,
        public string $size = 'md',
        public bool $dismissible = true,
        public bool $open = false,
    ) {
        $this->guiModal = ['id' => $id];
    }

    public function render(): View
    {
        return view('giraffeui::modal');
    }
}
