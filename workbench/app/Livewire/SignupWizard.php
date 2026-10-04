<?php

namespace Workbench\App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;

class SignupWizard extends Component
{
    /** @var array<string, string> */
    public const STEPS = ['account' => 'Account', 'workspace' => 'Workspace', 'review' => 'Review'];

    // Locked so the browser can't skip ahead; next() and goTo() are the only ways to move.
    #[Locked]
    public string $step = 'account';

    public string $name = '';

    public string $email = '';

    public string $workspace = '';

    public string $plan = 'team';

    /**
     * @return array<string, array<string, string>>
     */
    protected function stepRules(): array
    {
        return [
            'account' => ['name' => 'required|min:3', 'email' => 'required|email'],
            'workspace' => ['workspace' => 'required|alpha_dash|min:3', 'plan' => 'required|in:solo,team,business'],
            'review' => [],
        ];
    }

    public function next(): void
    {
        $this->validate($this->stepRules()[$this->step]);

        $this->moveTo($this->offset(1));
    }

    public function back(): void
    {
        $this->moveTo($this->offset(-1));
    }

    /**
     * Going forward has to pass through next(), so only earlier steps can be picked.
     */
    public function goTo(string $step): void
    {
        $keys = array_keys(self::STEPS);
        $index = array_search($step, $keys, true);

        if ($index !== false && $index < array_search($this->step, $keys, true)) {
            $this->moveTo($step);
        }
    }

    public function finish(): void
    {
        // Check everything again; send people back to the first step with a problem.
        foreach ($this->stepRules() as $step => $rules) {
            $errors = validator($this->all(), $rules)->errors();

            if ($errors->isNotEmpty()) {
                $this->moveTo($step);
                $this->setErrorBag($errors);

                return;
            }
        }

        $this->dispatch('toast', message: "Created the {$this->workspace} workspace.", color: 'success');
        $this->reset();
    }

    protected function moveTo(string $step): void
    {
        $this->step = $step;
        $this->resetValidation();

        // Move focus to the new step's heading so keyboard and screen reader users start there.
        $this->js("document.getElementById('wizard-heading')?.focus()");
    }

    protected function offset(int $by): string
    {
        $keys = array_keys(self::STEPS);

        return $keys[max(0, min(count($keys) - 1, array_search($this->step, $keys, true) + $by))];
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div class="mx-auto max-w-lg space-y-8">
                <x-gui::stepper aria-label="Sign up">
                    @php($current = array_search($step, array_keys(self::STEPS)))
                    @foreach (self::STEPS as $key => $label)
                        @if ($loop->index < $current)
                            <x-gui::stepper.step status="complete" wire:click="goTo('{{ $key }}')">{{ $label }}</x-gui::stepper.step>
                        @else
                            <x-gui::stepper.step :status="$loop->index === $current ? 'current' : 'upcoming'">{{ $label }}</x-gui::stepper.step>
                        @endif
                    @endforeach
                </x-gui::stepper>

                <form wire:submit="{{ $step === 'review' ? 'finish' : 'next' }}" class="space-y-4">
                    <h3 id="wizard-heading" tabindex="-1" class="text-lg font-semibold focus:outline-none">{{ self::STEPS[$step] }}</h3>

                    @if ($step === 'account')
                        <x-gui::field label="Name" name="name" id="wizard-name">
                            <x-gui::input wire:model="name" autocomplete="name" />
                        </x-gui::field>

                        <x-gui::field label="Email" name="email" id="wizard-email">
                            <x-gui::input type="email" wire:model="email" autocomplete="email" />
                        </x-gui::field>
                    @elseif ($step === 'workspace')
                        <x-gui::field label="Workspace" name="workspace" id="wizard-workspace" hint="Letters, numbers, dashes and underscores.">
                            <x-gui::input wire:model="workspace" />
                        </x-gui::field>

                        <x-gui::fieldset legend="Plan" name="plan">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <x-gui::radio variant="card" wire:model="plan" name="plan" value="solo" label="Solo" description="Just you" />
                                <x-gui::radio variant="card" wire:model="plan" name="plan" value="team" label="Team" description="Up to 10" />
                                <x-gui::radio variant="card" wire:model="plan" name="plan" value="business" label="Business" description="Unlimited" />
                            </div>
                        </x-gui::fieldset>
                    @else
                        <x-gui::description-list :items="['Name' => $name, 'Email' => $email, 'Workspace' => $workspace, 'Plan' => ucfirst($plan)]" />
                    @endif

                    <div class="flex justify-between gap-3 pt-2">
                        @if ($step !== 'account')
                            <x-gui::button variant="outline" color="secondary" wire:click="back">Back</x-gui::button>
                        @endif
                        <x-gui::button type="submit" class="ml-auto">{{ $step === 'review' ? 'Create workspace' : 'Continue' }}</x-gui::button>
                    </div>
                </form>
            </div>
            BLADE;
    }
}
