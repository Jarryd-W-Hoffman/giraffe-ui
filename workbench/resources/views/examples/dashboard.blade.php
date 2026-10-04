@extends('catalog::examples.layout')

@section('content')
    @php
        $orders = [
            ['id' => '#1048', 'customer' => 'Ada Lovelace', 'date' => '5 Oct 2026', 'status' => 'Paid', 'total' => '$248.00'],
            ['id' => '#1047', 'customer' => 'Grace Hopper', 'date' => '5 Oct 2026', 'status' => 'Pending', 'total' => '$96.50'],
            ['id' => '#1046', 'customer' => 'Alan Turing', 'date' => '4 Oct 2026', 'status' => 'Paid', 'total' => '$1,240.00'],
            ['id' => '#1045', 'customer' => 'Katherine Johnson', 'date' => '4 Oct 2026', 'status' => 'Refunded', 'total' => '$32.00'],
            ['id' => '#1044', 'customer' => 'Margaret Hamilton', 'date' => '3 Oct 2026', 'status' => 'Paid', 'total' => '$410.75'],
        ];
        $statusColors = ['Paid' => 'success', 'Pending' => 'warning', 'Refunded' => 'secondary'];
    @endphp

    <x-example::app-shell>
        <div class="mx-auto max-w-6xl space-y-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">How the shop is doing this month.</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-gui::segmented-control name="range" label="Period" size="sm" :options="['7d' => '7 days', '30d' => '30 days', '12m' => '12 months']" value="30d" />
                    <x-gui::button size="sm" x-on:click="GiraffeUI.toast('Report exported', { color: 'success' })">
                        <x-gui::icon name="arrow-down" size="sm" />
                        Export
                    </x-gui::button>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-gui::card>
                    <x-gui::stat label="Revenue" value="$48,200" change="12%" trend="up" description="vs last month" />
                </x-gui::card>
                <x-gui::card>
                    <x-gui::stat label="Orders" value="1,204" change="4%" trend="up" description="vs last month" />
                </x-gui::card>
                <x-gui::card>
                    <x-gui::stat label="Refund rate" value="1.8%" change="0.3 pts" trend="down" invert description="vs last month" />
                </x-gui::card>
                <x-gui::card>
                    <x-gui::stat label="Avg. response" value="2.4 h" change="0.6 h" trend="up" invert description="vs last month" />
                </x-gui::card>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <x-gui::card title="Recent orders" description="The last five orders across all channels." class="self-start xl:col-span-2">
                    <x-slot:actions>
                        <x-gui::button href="#" variant="outline" color="secondary" size="sm">View all</x-gui::button>
                    </x-slot:actions>

                    <x-gui::table label="Recent orders">
                        <x-slot:head>
                            <x-gui::table.heading>Order</x-gui::table.heading>
                            <x-gui::table.heading>Customer</x-gui::table.heading>
                            <x-gui::table.heading class="hidden sm:table-cell">Date</x-gui::table.heading>
                            <x-gui::table.heading>Status</x-gui::table.heading>
                            <x-gui::table.heading class="text-right">Total</x-gui::table.heading>
                        </x-slot:head>

                        @foreach ($orders as $order)
                            <x-gui::table.row>
                                <x-gui::table.cell class="font-medium">{{ $order['id'] }}</x-gui::table.cell>
                                <x-gui::table.cell>{{ $order['customer'] }}</x-gui::table.cell>
                                <x-gui::table.cell class="hidden sm:table-cell">{{ $order['date'] }}</x-gui::table.cell>
                                <x-gui::table.cell><x-gui::badge size="sm" :color="$statusColors[$order['status']]">{{ $order['status'] }}</x-gui::badge></x-gui::table.cell>
                                <x-gui::table.cell class="text-right tabular-nums">{{ $order['total'] }}</x-gui::table.cell>
                            </x-gui::table.row>
                        @endforeach
                    </x-gui::table>
                </x-gui::card>

                <div class="space-y-6">
                    <x-gui::card title="Monthly goals">
                        <div class="space-y-5">
                            <x-gui::progress :value="48200" :max="60000" label="Revenue: $48,200 of $60,000" show-value />
                            <x-gui::progress :value="1204" :max="1500" label="Orders: 1,204 of 1,500" show-value />
                            <x-gui::progress :value="9" :max="10" label="New products: 9 of 10" show-value color="success" />
                        </div>
                    </x-gui::card>

                    <x-gui::card title="Team">
                        <div class="flex items-center justify-between gap-4">
                            {{-- The rings match the card, not the page. --}}
                            <x-gui::avatar.group :avatars="$users" :max="3" label="Team members" class="dark:*:ring-zinc-900!" />
                            <x-gui::button variant="outline" color="secondary" size="sm" commandfor="invite" command="show-modal">
                                <x-gui::icon name="plus" size="sm" />
                                Invite
                            </x-gui::button>
                        </div>
                    </x-gui::card>

                    <x-gui::alert color="warning" title="Payout on hold">
                        Confirm your bank details to receive this week's payout.
                    </x-gui::alert>
                </div>
            </div>
        </div>
    </x-example::app-shell>

    <x-gui::modal id="invite" title="Invite a teammate" description="They'll get an email with a link to join." size="sm">
        <form id="invite-form" class="space-y-4" onsubmit="event.preventDefault(); this.closest('dialog').close(); GiraffeUI.toast('Invitation sent', { color: 'success' })">
            <x-gui::field label="Email" name="invite_email" required>
                <x-gui::input type="email" placeholder="name@example.com" />
            </x-gui::field>
            <x-gui::field label="Role" name="invite_role">
                <x-gui::select :options="['admin' => 'Admin', 'editor' => 'Editor', 'viewer' => 'Viewer']" value="editor" />
            </x-gui::field>
        </form>
        <x-slot:footer>
            <x-gui::modal.close>Cancel</x-gui::modal.close>
            <x-gui::button type="submit" form="invite-form">Send invite</x-gui::button>
        </x-slot:footer>
    </x-gui::modal>
@endsection
