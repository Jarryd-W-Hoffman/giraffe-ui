@extends('catalog::examples.layout')

@section('content')
    @php
        $keys = array_keys($steps);
        $index = array_search($step, $keys, true);
        $previous = $keys[$index - 1] ?? null;
        $headings = [
            'workspace' => ['Create your workspace', 'A home for your shop. You can rename it later.'],
            'team' => ['Invite your team', 'They get an email with a link to join. You can skip this for now.'],
            'preferences' => ['Set your preferences', 'Used for reports and the emails we send you.'],
            'review' => ['Check everything', 'Go back to any step to change it.'],
        ];
        $timezones = [($tz = old('timezone', $data['timezone'] ?? 'Europe/London')) => str_replace('_', ' ', $tz)] + $timezones;
    @endphp

    <div class="min-h-dvh bg-zinc-50 dark:bg-zinc-950">
        <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto flex max-w-2xl items-center gap-2 px-4 py-4 font-semibold sm:px-6">
                <span class="flex size-7 items-center justify-center rounded-gui bg-gui-primary text-sm text-gui-primary-foreground" aria-hidden="true">A</span>
                Acme
            </div>
        </header>

        <main class="mx-auto max-w-2xl space-y-8 px-4 py-10 sm:px-6">
            {{-- Steps already done are links back; the rest aren't, so nobody skips ahead. --}}
            <x-gui::stepper aria-label="Setup" :steps="$steps" :current="$step" />

            <x-gui::card>
                <div class="mb-6">
                    <h1 class="text-xl font-semibold tracking-tight">{{ $headings[$step][0] }}</h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $headings[$step][1] }}</p>
                </div>

                @if ($errors->any())
                    <x-gui::alert color="danger" title="Check this step" class="mb-6">Fix the {{ $errors->count() === 1 ? 'field' : 'fields' }} below to carry on.</x-gui::alert>
                @endif

                {{-- A real form post per step: no JavaScript needed to get through. novalidate shows the server's checks. --}}
                <form id="onboarding-form" method="POST" action="/catalog/examples/onboarding" class="space-y-5" novalidate>
                    @csrf
                    <input type="hidden" name="step" value="{{ $step }}">

                    @if ($step === 'workspace')
                        <x-gui::field label="Workspace name" name="workspace" required>
                            <x-gui::input :value="old('workspace', $data['workspace'] ?? null)" placeholder="Analytical Engines" />
                        </x-gui::field>

                        <x-gui::field label="Web address" name="slug" hint="Lowercase letters, numbers and dashes." required>
                            <x-gui::input prefix="acme.app/" :value="old('slug', $data['slug'] ?? null)" placeholder="analytical-engines" autocapitalize="none" spellcheck="false" />
                        </x-gui::field>
                    @elseif ($step === 'team')
                        <x-gui::field label="Email addresses" name="invites" hint="One per line, or separated by commas.">
                            <x-gui::textarea rows="3" autosize placeholder="grace@example.com">{{ old('invites', implode("\n", $data['invites'] ?? [])) }}</x-gui::textarea>
                        </x-gui::field>

                        <x-gui::field label="They join as" name="role">
                            <x-gui::segmented-control name="role" :options="$roles" :value="old('role', $data['role'] ?? 'member')" />
                        </x-gui::field>
                    @elseif ($step === 'preferences')
                        <x-gui::field label="Timezone" name="timezone" hint="Try Tokyo or America." required>
                            <x-gui::combobox url="/catalog/api/timezones" :options="$timezones" :value="old('timezone', $data['timezone'] ?? 'Europe/London')" placeholder="Search timezones" />
                        </x-gui::field>

                        <x-gui::fieldset legend="Email me">
                            <x-gui::switch name="digest" value="1" label="A weekly summary" description="Sales, orders and refunds, every Monday." :checked="(bool) old('digest', $data['digest'] ?? true)" />
                            <x-gui::switch name="announcements" value="1" label="Product news" description="A few times a year." :checked="(bool) old('announcements', $data['announcements'] ?? false)" />
                        </x-gui::fieldset>
                    @else
                        <x-gui::description-list :items="[
                            'Workspace' => $data['workspace'],
                            'Web address' => 'acme.app/'.$data['slug'],
                            'Invites' => $data['invites'] ? implode(', ', $data['invites']) : 'Nobody yet',
                            'They join as' => $roles[$data['role']],
                            'Timezone' => str_replace('_', ' ', $data['timezone']),
                            'Emails' => implode(', ', array_keys(array_filter(['Weekly summary' => $data['digest'], 'Product news' => $data['announcements']]))) ?: 'None',
                        ]" />
                    @endif
                </form>

                <x-slot:footer>
                    <div class="flex w-full items-center justify-between gap-3">
                        @if ($previous)
                            <x-gui::button href="/catalog/examples/onboarding?step={{ $previous }}" variant="ghost" color="secondary">
                                <x-gui::icon name="arrow-left" size="sm" />
                                Back
                            </x-gui::button>
                        @endif
                        <x-gui::button type="submit" form="onboarding-form" class="ms-auto">{{ $step === 'review' ? 'Create workspace' : 'Continue' }}</x-gui::button>
                    </div>
                </x-slot:footer>
            </x-gui::card>

            <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">Step {{ $index + 1 }} of {{ count($steps) }}</p>
        </main>
    </div>
@endsection
