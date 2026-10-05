<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * A searchable list of pages and actions in a native <dialog>, opened with ⌘K / Ctrl+K.
 *
 * A class so groups and items can find the palette through a uniquely named @aware key.
 */
class CommandPalette extends Component
{
    /**
     * @var array{id: string}
     */
    public array $guiCommandPalette;

    public function __construct(
        public string $id = 'command-palette',
        public ?string $shortcut = 'k',
        public ?string $placeholder = null,
        public ?string $empty = null,
        public ?string $label = null,
    ) {
        $this->placeholder ??= __('giraffeui::ui.search');
        $this->empty ??= __('giraffeui::ui.no_results');
        $this->label ??= __('giraffeui::ui.command_palette');
        $this->guiCommandPalette = ['id' => $id];
    }

    public function render(): View
    {
        return view('giraffeui::command-palette');
    }
}
