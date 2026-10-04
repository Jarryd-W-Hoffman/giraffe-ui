<?php

namespace Workbench\App\Livewire;

use Livewire\Component;

class SettingsTabs extends Component
{
    public string $tab = 'notifications';

    public function render(): string
    {
        return <<<'BLADE'
            <div class="mx-auto max-w-md space-y-4">
                <x-gui::tabs wire:model.live="tab" :tabs="['account' => 'Account', 'notifications' => 'Notifications', 'billing' => 'Billing']" :selected="$tab">
                    <x-gui::tabs.panel name="account">Account settings</x-gui::tabs.panel>
                    <x-gui::tabs.panel name="notifications">Notification settings</x-gui::tabs.panel>
                    <x-gui::tabs.panel name="billing">Billing settings</x-gui::tabs.panel>
                </x-gui::tabs>

                <p class="text-sm text-zinc-600 dark:text-zinc-400">On the server, <code>$tab</code> is <x-gui::badge size="sm" color="primary">{{ $tab }}</x-gui::badge></p>

                <x-gui::button size="sm" variant="outline" color="secondary" wire:click="$set('tab', 'billing')">Jump to billing from the server</x-gui::button>
            </div>
            BLADE;
    }
}
