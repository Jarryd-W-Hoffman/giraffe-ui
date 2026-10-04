<?php

namespace Workbench\App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;

class ProfileForm extends Component
{
    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required', message: 'Choose a country.')]
    public string $country = '';

    public function save(): void
    {
        $this->validate();

        // Pretend to do some work so the loading state is visible.
        usleep(800_000);

        $this->dispatch('toast', message: "Saved {$this->name}'s profile.", color: 'success');
    }

    public function render(): string
    {
        return <<<'BLADE'
            <form wire:submit="save" class="mx-auto max-w-sm space-y-4">
                <x-gui::field label="Name" name="name" required>
                    <x-gui::input wire:model="name" />
                </x-gui::field>

                <x-gui::field label="Email" name="email" required>
                    <x-gui::input type="email" wire:model="email" />
                </x-gui::field>

                <x-gui::field label="Country" name="country">
                    <x-gui::combobox wire:model="country" :options="\Workbench\App\Catalog::countries()" placeholder="Search countries" />
                </x-gui::field>

                <x-gui::button type="submit">Save profile</x-gui::button>
            </form>
            BLADE;
    }
}
