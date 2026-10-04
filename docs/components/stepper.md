# Stepper

_Generated from `workbench/app/Catalog.php`. Edit that file, then run `composer docs`._

Shows progress through a multi-step flow such as a signup or checkout wizard. Steps are complete, current, upcoming or need attention.

```blade
<x-gui::stepper :steps="['account' => 'Account', 'shipping' => 'Shipping', 'review' => 'Review']" current="shipping" />
```

## With descriptions and links back

Steps before the current one are complete. A complete step with an href links back to it; later steps never link, so people can't skip ahead. Below the sm breakpoint, horizontal descriptions are kept for screen readers only.

```blade
<x-gui::stepper current="plan" :steps="[
    'account' => ['label' => 'Account', 'description' => 'Your details', 'href' => '#'],
    'plan' => ['label' => 'Plan', 'description' => 'Pick a plan'],
    'invite' => ['label' => 'Invite', 'description' => 'Add your team'],
]" />
```

## Vertical

```blade
<x-gui::stepper orientation="vertical" current="deploy" :steps="[
    'repo' => ['label' => 'Connect a repository', 'description' => 'GitHub, GitLab or Bitbucket.'],
    'build' => ['label' => 'Configure the build', 'description' => 'We detected Laravel.'],
    'deploy' => ['label' => 'Deploy', 'description' => 'Your first deploy takes about a minute.'],
    'domain' => ['label' => 'Add a domain', 'description' => 'Optional.'],
]" />
```

## Steps that need attention

Pass the keys of steps with problems to invalid, for example after validating the whole form at the end.

```blade
<x-gui::stepper current="review" :invalid="['shipping']" :steps="['cart' => 'Cart', 'shipping' => 'Shipping', 'review' => 'Review']" />
```

## Composed

Set each step's status yourself. A step with a click handler (wire:click, x-on:click) renders as a button, which suits Livewire wizards; see the Livewire page.

```blade
<x-gui::stepper aria-label="Checkout">
    <x-gui::stepper.step status="complete" x-on:click="alert('Back to the cart')">Cart</x-gui::stepper.step>
    <x-gui::stepper.step status="current">Payment</x-gui::stepper.step>
    <x-gui::stepper.step>Confirmation</x-gui::stepper.step>
</x-gui::stepper>
```

## Props

| Prop | Type | Default | Description |
|---|---|---|---|
| `steps` | `array` | `null` | key =&gt; label, or key =&gt; [label, description, href]. Or compose stepper.step. |
| `current` | `string \| int` | `first key` | Key of the current step. Earlier steps are complete. |
| `complete` | `bool` | `false` | Mark every step complete, for a finished flow. |
| `invalid` | `array` | `[]` | Keys of steps that need attention. |
| `orientation` | `'horizontal' \| 'vertical'` | `'horizontal'` | Horizontal puts labels under the indicators; vertical puts them beside. |
| `aria-label` | `string` | `'Progress'` | Name of the navigation landmark. |
| `status (step)` | `'complete' \| 'current' \| 'upcoming' \| 'error'` | `'upcoming'` | The step's state. |
| `current (step)` | `bool` | `status is current` | Set aria-current="step" on a step with another status, such as error. |
| `description (step)` | `string` | `null` | Secondary text under the label. |
| `href (step)` | `string` | `null` | Makes the step a link. |

The current step has aria-current="step". Complete steps and steps that need attention add "completed" or "needs attention" to their label for screen readers, since the check mark and colours are hidden from them.

Step numbers come from a CSS counter, so composed steps number themselves.

The stepper only shows progress; your form decides which step is showing. When moving between steps, put focus on the new step's heading.

Any other attribute, such as `class`, `wire:model` or `x-on:click`, is passed through to the element. To replace one of the component's own classes, use Tailwind's `!` modifier, e.g. `class="px-0!"`.

## Slots

| Slot | Description |
|---|---|
| `default` | stepper.step elements, when not using steps. |
