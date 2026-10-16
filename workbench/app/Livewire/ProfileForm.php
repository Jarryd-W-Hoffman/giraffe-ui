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

    #[Validate('nullable|date|before:today', message: ['before' => 'Your birthday must be in the past.'])]
    public string $birthday = '';

    #[Validate('integer|min:1|max:500')]
    public int $teamSize = 5;

    #[Validate('integer|min:0|max:40')]
    public int $experience = 10;

    #[Validate('max:160')]
    public string $bio = 'I build things.';

    public function save(): void
    {
        $this->validate();

        // Pretend to do some work so the loading state is visible.
        usleep(800_000);

        $this->dispatch('gui-toast', message: "Saved {$this->name}'s profile.", color: 'success');
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

                <x-gui::field label="Birthday" name="birthday">
                    <x-gui::date-picker wire:model="birthday" />
                </x-gui::field>

                <x-gui::field label="Team size" name="teamSize">
                    <x-gui::number-input wire:model="teamSize" min="1" max="500" class="max-w-40" />
                </x-gui::field>

                <x-gui::field label="Years of experience" name="experience">
                    <x-gui::slider wire:model="experience" min="0" max="40" show-value suffix=" years" />
                </x-gui::field>

                <x-gui::field label="Bio" name="bio" hint="Shown on your profile.">
                    <x-gui::textarea wire:model="bio" rows="2" autosize counter maxlength="160" />
                </x-gui::field>

                <x-gui::button type="submit">Save profile</x-gui::button>
            </form>
            BLADE;
    }
}
