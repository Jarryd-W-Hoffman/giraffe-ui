@extends('catalog::examples.layout')

@section('content')
    @php
        $lines = [
            ['Website redesign', 'Design and build of the new marketing site, 6 pages', 1, 4800],
            ['Pricing page', 'A/B tested variants, two rounds', 1, 1200],
            ['Checkout flow', 'Payments, error states and receipts', 1, 2600],
            ['Support retainer', 'Hours in October', 12, 95],
        ];
        $subtotal = collect($lines)->sum(fn ($line) => $line[2] * $line[3]);
        $tax = round($subtotal * 0.2, 2);
        $total = $subtotal + $tax;
        $money = fn ($value) => '$'.number_format($value, 2);
    @endphp

    <x-example::app search="Search invoices">
        <div class="max-w-5xl space-y-8" x-data="{ paid: false }">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-gui::breadcrumbs :items="['Workspace' => '/catalog/examples/dashboard', 'Invoices' => '#', 'INV-0042' => null]" />
                    <h1 class="mt-3 flex flex-wrap items-center gap-4 text-4xl font-normal tracking-tight sm:text-5xl">
                        <span>Invoice <span class="text-zinc-500 dark:text-zinc-400">INV-0042</span></span>
                        <span x-show="! paid" class="tracking-normal"><x-gui::badge shape="pill" color="warning" dot="warning">Due in 9 days</x-gui::badge></span>
                        <span x-show="paid" x-cloak class="tracking-normal"><x-gui::badge shape="pill" color="success" dot="success">Paid</x-gui::badge></span>
                    </h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">To Globex Corporation, issued 13 October 2026.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-gui::button variant="outline" color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('Reminder sent to Globex')"><x-gui::icon name="envelope" size="sm" /> Send reminder</x-gui::button>
                    <x-gui::button variant="outline" color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('INV-0042.pdf downloaded', { color: 'success' })"><x-gui::icon name="arrow-down-tray" size="sm" /> Download PDF</x-gui::button>
                    <x-gui::button color="ink" shape="pill" size="sm" commandfor="record-payment" command="show-modal" x-bind:disabled="paid">Record payment</x-gui::button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <x-gui::card variant="filled">
                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">From</p>
                            <p class="mt-1 font-medium">Acme Studio Ltd</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">12 Analytical Way<br>London EC1V 9NR</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Bill to</p>
                            <p class="mt-1 font-medium">Globex Corporation</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">accounts@globex.example<br>Springfield, OR 97477</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Amount due</p>
                            <p class="mt-1 text-3xl font-semibold tracking-tight tabular-nums" x-text="paid ? '$0.00' : @js($money($total))">{{ $money($total) }}</p>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">by 22 October 2026</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <x-gui::table label="Line items">
                            <x-slot:head>
                                <x-gui::table.heading>Item</x-gui::table.heading>
                                <x-gui::table.heading align="right" class="hidden sm:table-cell">Qty</x-gui::table.heading>
                                <x-gui::table.heading align="right" class="hidden sm:table-cell">Rate</x-gui::table.heading>
                                <x-gui::table.heading align="right">Amount</x-gui::table.heading>
                            </x-slot:head>
                            @foreach ($lines as [$item, $detail, $qty, $rate])
                                <x-gui::table.row>
                                    <x-gui::table.cell wrap>
                                        <span class="block font-medium">{{ $item }}</span>
                                        <span class="block text-sm text-zinc-600 dark:text-zinc-400">{{ $detail }}</span>
                                    </x-gui::table.cell>
                                    <x-gui::table.cell align="right" class="hidden tabular-nums sm:table-cell">{{ $qty }}</x-gui::table.cell>
                                    <x-gui::table.cell align="right" class="hidden tabular-nums sm:table-cell">{{ $money($rate) }}</x-gui::table.cell>
                                    <x-gui::table.cell align="right" class="tabular-nums">{{ $money($qty * $rate) }}</x-gui::table.cell>
                                </x-gui::table.row>
                            @endforeach
                        </x-gui::table>
                    </div>

                    <div class="mt-6 ms-auto max-w-xs">
                        <x-gui::description-list :items="['Subtotal' => $money($subtotal), 'VAT (20%)' => $money($tax), 'Total' => $money($total)]" />
                    </div>

                    <x-slot:footer>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pay by bank transfer to Acme Studio Ltd, sort code 12-34-56, account 87654321, quoting INV-0042. Thank you!</p>
                    </x-slot:footer>
                </x-gui::card>

                <div class="space-y-4">
                    <x-gui::card variant="filled" title="Activity" icon="clock">
                        <x-gui::timeline label="Invoice activity">
                            <x-gui::timeline.item title="Paid in full" time="Just now" icon="check" color="success" x-show="paid" x-cloak />
                            <x-gui::timeline.item title="Viewed by Globex" time="14 Oct" icon="eye" />
                            <x-gui::timeline.item title="Sent to accounts@globex.example" time="13 Oct" icon="paper-airplane" />
                            <x-gui::timeline.item title="Created by Ada" time="13 Oct" icon="document-text" />
                        </x-gui::timeline>
                    </x-gui::card>

                    <x-gui::card variant="filled" title="Payment" icon="credit-card">
                        <div x-show="! paid"><x-gui::progress :value="0" label="Paid so far" show-value /></div>
                        <div x-show="paid" x-cloak><x-gui::progress :value="100" label="Paid so far" show-value color="success" /></div>
                        <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">Globex usually pays in 12 days.</p>
                    </x-gui::card>
                </div>
            </div>

            <x-gui::modal id="record-payment" title="Record a payment" description="For INV-0042, to Globex Corporation." size="sm">
                <form id="record-payment-form" class="space-y-4" x-on:submit.prevent="paid = true; $el.closest('dialog').close(); GiraffeUI.toast('Payment recorded', { color: 'success' })">
                    <x-gui::field label="Amount" name="payment_amount" required>
                        <x-gui::input type="number" prefix="$" step="0.01" :value="number_format($total, 2, '.', '')" />
                    </x-gui::field>
                    <x-gui::field label="Paid on" name="payment_date" required>
                        <x-gui::date-picker value="2026-10-13" />
                    </x-gui::field>
                    <x-gui::field label="Method" name="payment_method">
                        <x-gui::select :options="['bank' => 'Bank transfer', 'card' => 'Card', 'other' => 'Other']" value="bank" />
                    </x-gui::field>
                </form>
                <x-slot:footer>
                    <x-gui::modal.close>Cancel</x-gui::modal.close>
                    <x-gui::button type="submit" form="record-payment-form" color="ink" shape="pill">Record payment</x-gui::button>
                </x-slot:footer>
            </x-gui::modal>
        </div>
    </x-example::app>
@endsection
