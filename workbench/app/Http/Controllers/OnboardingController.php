<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Workbench\App\Catalog;

/**
 * The Onboarding example: a multi-step form with the stepper, built on plain form posts. Each step validates its own
 * fields and keeps them in the session; the URL says which step to show, but never one that hasn't been reached.
 */
class OnboardingController
{
    public const STEPS = ['workspace' => 'Workspace', 'team' => 'Team', 'preferences' => 'Preferences', 'review' => 'Review'];

    public const ROLES = ['member' => 'Member', 'admin' => 'Admin', 'viewer' => 'Viewer'];

    public function show(Request $request): View
    {
        $data = $request->session()->get('onboarding', []);
        $keys = array_keys(self::STEPS);
        $reached = $this->reached($data);

        // Asking for a step that hasn't been reached shows the furthest one that has.
        $step = in_array($request->query('step'), $keys, true) && array_search($request->query('step'), $keys, true) <= $reached
            ? $request->query('step')
            : $keys[$reached];

        return view('catalog::examples.onboarding', Catalog::data() + [
            'title' => 'Onboarding',
            'step' => $step,
            'steps' => collect(self::STEPS)->map(fn (string $label, string $key) => [
                'label' => $label,
                // Steps already done link back; the current and later ones don't.
                'href' => array_search($key, $keys, true) < array_search($step, $keys, true) ? "/catalog/examples/onboarding?step={$key}" : null,
            ])->all(),
            'data' => $data,
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $step = $request->input('step');
        abort_unless(isset(self::STEPS[$step]), 404);

        if ($step === 'review') {
            $data = $request->session()->pull('onboarding', []);
            abort_unless($this->reached($data) === 3, 409);

            return redirect('/catalog/examples/dashboard')->with('toast', ['message' => "Created the {$data['workspace']} workspace.", 'color' => 'success']);
        }

        $validated = $request->validate($this->rules($step), $this->messages());

        if ($step === 'team') {
            // One address per line or comma, tidied and deduplicated.
            $validated['invites'] = $this->emails($validated['invites'] ?? '');
        }

        if ($step === 'preferences') {
            $validated['digest'] = $request->boolean('digest');
            $validated['announcements'] = $request->boolean('announcements');
        }

        $request->session()->put('onboarding', array_merge($request->session()->get('onboarding', []), $validated));

        $next = array_keys(self::STEPS)[array_search($step, array_keys(self::STEPS), true) + 1];

        return redirect("/catalog/examples/onboarding?step={$next}");
    }

    /**
     * The index of the furthest step whose earlier steps are all done.
     *
     * @param  array<string, mixed>  $data
     */
    protected function reached(array $data): int
    {
        return match (true) {
            ! Arr::has($data, ['workspace', 'slug']) => 0,
            ! Arr::has($data, ['role']) => 1,
            ! Arr::has($data, ['timezone']) => 2,
            default => 3,
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(string $step): array
    {
        return [
            'workspace' => [
                'workspace' => ['required', 'string', 'max:50'],
                'slug' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/'],
            ],
            'team' => [
                'invites' => ['nullable', 'string', 'max:1000', function (string $attribute, mixed $value, \Closure $fail) {
                    foreach ($this->emails((string) $value) as $email) {
                        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail("\"{$email}\" isn't an email address.");

                            return;
                        }
                    }
                }],
                'role' => ['required', 'in:'.implode(',', array_keys(self::ROLES))],
            ],
            'preferences' => [
                'timezone' => ['required', 'timezone:all'],
            ],
        ][$step];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'workspace.required' => 'Give your workspace a name.',
            'slug.required' => 'Choose a web address.',
            'slug.regex' => 'Use lowercase letters, numbers and single dashes.',
            'timezone.required' => 'Choose a timezone.',
            'timezone.timezone' => 'Choose a timezone from the list.',
        ];
    }

    /**
     * @return list<string>
     */
    protected function emails(string $value): array
    {
        return collect(preg_split('/[\s,]+/', $value))->map(fn ($email) => Str::lower(trim($email)))->filter()->unique()->values()->all();
    }
}
