@extends('catalog::examples.layout')

@section('content')
    @php
        $plans = [
            'starter' => [
                'name' => 'Starter',
                'description' => 'For side projects and small shops.',
                'monthly' => 29,
                'yearly' => 23,
                'cta' => 'Start free trial',
                'features' => ['1 shop', 'Up to 500 orders a month', 'Email support', 'Basic reports'],
            ],
            'pro' => [
                'name' => 'Pro',
                'description' => 'For growing teams that sell every day.',
                'monthly' => 99,
                'yearly' => 79,
                'cta' => 'Start free trial',
                'features' => ['3 shops', 'Unlimited orders', 'Priority support', 'Advanced reports', 'Up to 10 teammates'],
                'popular' => true,
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'description' => 'For large catalogues and custom needs.',
                'cta' => 'Contact sales',
                'features' => ['Unlimited shops', 'Single sign-on', 'A dedicated manager', '99.99% uptime SLA', 'Unlimited teammates'],
            ],
        ];

        // true = included, false = not included, a string = the plan's limit.
        $comparison = [
            'Shops' => ['1', '3', 'Unlimited'],
            'Orders a month' => ['500', 'Unlimited', 'Unlimited'],
            'Teammates' => ['2', '10', 'Unlimited'],
            'Discount codes' => [true, true, true],
            'Advanced reports' => [false, true, true],
            'Priority support' => [false, true, true],
            'Single sign-on' => [false, false, true],
            'Uptime SLA' => [false, false, '99.99%'],
        ];

        $faqs = [
            'Can I change plans later?' => 'Yes. Upgrades apply straight away and we charge the difference for the rest of the period; downgrades apply at your next renewal.',
            'What happens when the trial ends?' => 'Nothing, unless you pick a plan. We don\'t ask for a card up front, so you\'ll never be charged by surprise.',
            'Do you offer discounts?' => 'Paying yearly saves about 20%. Charities and schools get a further 50% off; contact us with proof of status.',
            'Can I cancel at any time?' => 'Yes. Cancel from Settings and you keep access until the end of the period you paid for.',
        ];
    @endphp

    <x-gui::navbar sticky>
        <x-slot:brand>
            <a href="#" class="flex items-center gap-2 font-semibold">
                <span class="flex size-7 items-center justify-center rounded-gui bg-gui-primary text-sm text-gui-primary-foreground" aria-hidden="true">A</span>
                Acme
            </a>
        </x-slot:brand>

        <x-gui::navbar.item href="#">Product</x-gui::navbar.item>
        <x-gui::navbar.item href="#" current>Pricing</x-gui::navbar.item>
        <x-gui::navbar.item href="/catalog/introduction">Docs</x-gui::navbar.item>

        <x-slot:actions>
            <x-gui::button href="/catalog/examples/sign-in" variant="ghost" color="secondary" size="sm">Sign in</x-gui::button>
            <x-gui::button href="/catalog/examples/register" size="sm">Start free trial</x-gui::button>
        </x-slot:actions>
    </x-gui::navbar>

    <main>
        {{-- The billing switch is native radios; prices swap with :has(), so this works without JavaScript. --}}
        <section aria-labelledby="pricing-heading" class="group/billing mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold text-gui-primary-text">Pricing</p>
                <h1 id="pricing-heading" class="mt-2 text-3xl font-semibold tracking-tight text-balance sm:text-4xl">Simple plans that grow with your shop</h1>
                <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Every plan starts with a 14-day free trial. No card needed.</p>
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                <x-gui::segmented-control name="billing" label="Billing period" :options="['monthly' => 'Monthly', 'yearly' => 'Yearly']" value="monthly" />
                <x-gui::badge color="success">Save 20% yearly</x-gui::badge>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($plans as $key => $plan)
                    <div @class([
                        'relative flex flex-col rounded-gui border bg-white p-6 shadow-xs dark:bg-zinc-900',
                        'border-gui-primary-text ring-1 ring-gui-primary-text' => $plan['popular'] ?? false,
                        'border-zinc-200 dark:border-zinc-800' => ! ($plan['popular'] ?? false),
                    ])>
                        <div class="flex items-center justify-between gap-3">
                            <h2 id="plan-{{ $key }}" class="text-lg font-semibold">{{ $plan['name'] }}</h2>
                            @if ($plan['popular'] ?? false)
                                <x-gui::badge color="primary" variant="solid" size="sm">Most popular</x-gui::badge>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $plan['description'] }}</p>

                        <p class="mt-6 flex items-baseline gap-1">
                            @isset($plan['monthly'])
                                <span class="text-4xl font-semibold tracking-tight tabular-nums group-has-[[value=yearly]:checked]/billing:hidden">${{ $plan['monthly'] }}</span>
                                <span class="hidden text-4xl font-semibold tracking-tight tabular-nums group-has-[[value=yearly]:checked]/billing:inline">${{ $plan['yearly'] }}</span>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                    per month<span class="hidden group-has-[[value=yearly]:checked]/billing:inline">, billed yearly</span>
                                </span>
                            @else
                                <span class="text-4xl font-semibold tracking-tight">Custom</span>
                            @endisset
                        </p>

                        @isset($plan['monthly'])
                            <x-gui::button href="/catalog/examples/register" :variant="($plan['popular'] ?? false) ? 'solid' : 'outline'" :color="($plan['popular'] ?? false) ? 'primary' : 'secondary'" class="mt-6 w-full" aria-describedby="plan-{{ $key }}">{{ $plan['cta'] }}</x-gui::button>
                        @else
                            <x-gui::button variant="outline" color="secondary" class="mt-6 w-full" commandfor="contact-sales" command="show-modal">{{ $plan['cta'] }}</x-gui::button>
                        @endisset

                        <ul class="mt-8 space-y-3 text-sm" aria-label="{{ $plan['name'] }} includes">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex gap-3">
                                    <x-gui::icon name="check" size="sm" class="mt-0.5 text-gui-primary-text" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <section aria-labelledby="compare-heading" class="mx-auto max-w-4xl px-4 pb-16 sm:px-6 sm:pb-20">
            <h2 id="compare-heading" class="text-2xl font-semibold tracking-tight">Compare plans</h2>

            <x-gui::table label="Features by plan" class="mt-6">
                <x-slot:head>
                    <x-gui::table.heading><span class="sr-only">Feature</span></x-gui::table.heading>
                    @foreach ($plans as $plan)
                        <x-gui::table.heading class="text-center!">{{ $plan['name'] }}</x-gui::table.heading>
                    @endforeach
                </x-slot:head>

                @foreach ($comparison as $feature => $values)
                    <x-gui::table.row>
                        <th scope="row" class="px-4 py-3 text-left font-medium whitespace-nowrap text-zinc-900 dark:text-zinc-100">{{ $feature }}</th>
                        @foreach ($values as $value)
                            <x-gui::table.cell class="text-center">
                                @if ($value === true)
                                    <x-gui::icon name="check" size="sm" label="Included" class="inline text-gui-primary-text" />
                                @elseif ($value === false)
                                    <x-gui::icon name="minus" size="sm" label="Not included" class="inline text-zinc-400 dark:text-zinc-500" />
                                @else
                                    {{ $value }}
                                @endif
                            </x-gui::table.cell>
                        @endforeach
                    </x-gui::table.row>
                @endforeach
            </x-gui::table>
        </section>

        <section aria-labelledby="faq-heading" class="mx-auto max-w-3xl px-4 pb-16 sm:px-6 sm:pb-20">
            <h2 id="faq-heading" class="text-2xl font-semibold tracking-tight">Questions and answers</h2>

            <x-gui::accordion class="mt-6">
                @foreach ($faqs as $question => $answer)
                    <x-gui::accordion.item :title="$question" :open="$loop->first">{{ $answer }}</x-gui::accordion.item>
                @endforeach
            </x-gui::accordion>
        </section>

        <section class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-6 px-4 py-16 text-center sm:px-6">
                <h2 class="text-2xl font-semibold tracking-tight">Ready to sell more?</h2>
                <p class="max-w-md text-zinc-600 dark:text-zinc-400">Set up your shop in an afternoon. If Acme isn't for you, leave any time and take your data with you.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <x-gui::button href="/catalog/examples/register">Start free trial</x-gui::button>
                    <x-gui::button variant="outline" color="secondary" commandfor="contact-sales" command="show-modal">Talk to sales</x-gui::button>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-zinc-200 dark:border-zinc-800">
        <p class="mx-auto max-w-6xl px-4 py-8 text-sm text-zinc-600 sm:px-6 dark:text-zinc-400">© 2026 Acme, Inc. Prices in US dollars, before tax.</p>
    </footer>

    <x-gui::modal id="contact-sales" title="Talk to sales" description="Tell us about your shop and we'll reply within one working day.">
        <form id="contact-sales-form" class="space-y-4" onsubmit="event.preventDefault(); this.closest('dialog').close(); this.reset(); GiraffeUI.toast('Thanks! We\'ll be in touch soon.', { color: 'success' })">
            <x-gui::field label="Work email" name="sales_email" required>
                <x-gui::input type="email" autocomplete="email" placeholder="name@company.com" />
            </x-gui::field>
            <x-gui::field label="Company" name="sales_company" required>
                <x-gui::input autocomplete="organization" />
            </x-gui::field>
            <x-gui::field label="Team size" name="sales_team">
                <x-gui::number-input min="1" max="10000" value="25" class="max-w-40" />
            </x-gui::field>
            <x-gui::field label="What do you need?" name="sales_message">
                <x-gui::textarea rows="3" autosize counter maxlength="500" />
            </x-gui::field>
        </form>
        <x-slot:footer>
            <x-gui::modal.close>Cancel</x-gui::modal.close>
            <x-gui::button type="submit" form="contact-sales-form">Send</x-gui::button>
        </x-slot:footer>
    </x-gui::modal>
@endsection
