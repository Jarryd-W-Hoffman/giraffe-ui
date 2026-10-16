<?php

namespace Workbench\App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AvatarUpload extends Component
{
    use WithFileUploads;

    #[Validate('required|image|max:2048', message: ['required' => 'Choose an image to upload.', 'image' => 'The avatar must be an image.'])]
    public $avatar;

    public function save(): void
    {
        $this->validate();

        $this->dispatch('gui-toast', message: 'Uploaded '.$this->avatar->getClientOriginalName().'.', color: 'success');

        $this->reset('avatar');
    }

    public function render(): string
    {
        return <<<'BLADE'
            <form wire:submit="save" class="mx-auto max-w-sm space-y-4">
                <x-gui::field label="Avatar" name="avatar">
                    <x-gui::file-upload wire:model="avatar" accept="image/*" hint="An image, up to 2 MB." />
                </x-gui::field>

                <x-gui::button type="submit">Upload</x-gui::button>
            </form>
            BLADE;
    }
}
