<?php

namespace Workbench\App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Workbench\App\Catalog;

/**
 * The combobox's server-side search inside a Livewire form. The search goes to an ordinary route
 * (url), not through Livewire; wire:model binds the chosen value as usual.
 */
class TimezonePicker extends Component
{
    #[Validate('required|timezone:all')]
    public string $timezone = 'Europe/London';

    /**
     * What the list shows before anything is typed: the current choice, then the first timezones.
     *
     * @return array<string, string>
     */
    public function initialTimezones(): array
    {
        return [$this->timezone => str_replace('_', ' ', $this->timezone)] + collect(Catalog::timezones())->pluck('label', 'value')->all();
    }

    public function save(): void
    {
        $this->validate();

        $this->dispatch('gui-toast', message: "Saved {$this->timezone}.", color: 'success');
    }

    public function render(): string
    {
        return <<<'BLADE'
            <form wire:submit="save" class="mx-auto max-w-sm space-y-4">
                <x-gui::field label="Timezone" name="timezone" id="lw-timezone" hint="Try Tokyo or America.">
                    <x-gui::combobox wire:model="timezone" url="/catalog/api/timezones" :options="$this->initialTimezones()" placeholder="Search timezones" />
                </x-gui::field>

                <p class="text-sm text-zinc-600 dark:text-zinc-400">On the server, <code>$timezone</code> is <code>{{ $timezone ?: 'empty' }}</code>.</p>

                <x-gui::button type="submit">Save timezone</x-gui::button>
            </form>
            BLADE;
    }
}
