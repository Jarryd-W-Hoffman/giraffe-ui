<?php

namespace JayAitch\GiraffeUI\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * Tabs following the WAI-ARIA tabs pattern. Needs Alpine (Livewire includes it).
 */
class Tabs extends Component
{
    /**
     * @var array{id: string, selected: ?string}
     */
    public array $guiTabs;

    /**
     * @param  array<string, string>  $tabs  name => label
     */
    public function __construct(
        public array $tabs = [],
        public ?string $selected = null,
        public string $variant = 'underline',
        ?string $id = null,
    ) {
        $this->selected ??= array_key_first($tabs);
        $this->guiTabs = [
            'id' => $id ?? 'gui-tabs-'.Str::lower(Str::random(8)),
            'selected' => $this->selected,
        ];
    }

    public function tabId(string $name): string
    {
        return $this->guiTabs['id'].'-tab-'.Str::slug($name);
    }

    public function panelId(string $name): string
    {
        return $this->guiTabs['id'].'-panel-'.Str::slug($name);
    }

    public function render(): View
    {
        return view('giraffeui::tabs');
    }
}
