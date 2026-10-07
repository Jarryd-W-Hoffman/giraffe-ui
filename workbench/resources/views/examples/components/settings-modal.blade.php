{{--
    Settings, as a large modal over whichever page you're on: open it from the sidebar or the account menu with
    commandfor="settings" command="show-modal". Profile, notifications and account, each saved on its own.
--}}
@php
    $countries = \Workbench\App\Catalog::data()['countries'];
@endphp

<x-gui::modal id="settings" title="Settings" description="Manage your profile, notifications and account." size="xl">
    <x-gui::tabs variant="pills" :tabs="['profile' => 'Profile', 'notifications' => 'Notifications', 'account' => 'Account']">
        <x-gui::tabs.panel name="profile">
            <div class="space-y-5">
                <form id="profile-form" class="space-y-5" x-on:submit.prevent="GiraffeUI.toast('Profile saved', { color: 'success' })">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-gui::field label="Name" name="name" required>
                            <x-gui::input value="Ada Lovelace" autocomplete="name" />
                        </x-gui::field>
                        <x-gui::field label="Email" name="email" required>
                            <x-gui::input type="email" value="ada@example.com" autocomplete="email" />
                        </x-gui::field>
                    </div>

                    <x-gui::field label="Bio" name="bio" hint="A sentence or two about you.">
                        <x-gui::textarea rows="2" autosize counter maxlength="160">Analyst, metaphysician and founder of scientific computing.</x-gui::textarea>
                    </x-gui::field>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-gui::field label="Country" name="country">
                            <x-gui::combobox :options="$countries" value="gb" placeholder="Search countries" />
                        </x-gui::field>
                        <x-gui::field label="Birthday" name="birthday">
                            <x-gui::date-picker value="1815-12-10" />
                        </x-gui::field>
                    </div>

                    <x-gui::field label="Photo" name="photo">
                        <x-gui::file-upload accept="image/*" hint="PNG or JPG, up to 2 MB." />
                    </x-gui::field>
                </form>
                <div class="flex justify-end"><x-gui::button color="ink" shape="pill" type="submit" form="profile-form">Save profile</x-gui::button></div>
            </div>
        </x-gui::tabs.panel>

        <x-gui::tabs.panel name="notifications">
            <div class="space-y-5">
                <form id="notifications-form" class="space-y-8" x-on:submit.prevent="GiraffeUI.toast('Notification settings saved', { color: 'success' })">
                    <x-gui::fieldset legend="Email me about">
                        <x-gui::switch name="mentions" label="Mentions" description="When someone mentions you in a comment." checked />
                        <x-gui::switch name="orders" label="New orders" description="Every order, as it comes in." />
                        <x-gui::switch name="product" label="Product news" description="A few times a year." checked />
                    </x-gui::fieldset>

                    <x-gui::fieldset legend="How often" name="frequency">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <x-gui::radio variant="card" name="frequency" value="instant" label="Instantly" description="As things happen." />
                            <x-gui::radio variant="card" name="frequency" value="daily" label="Daily" description="One email a day." checked />
                            <x-gui::radio variant="card" name="frequency" value="weekly" label="Weekly" description="On Mondays." />
                        </div>
                    </x-gui::fieldset>

                    <div class="space-y-3">
                        <p id="quiet-days-label" class="text-sm font-medium">Quiet days</p>
                        <x-gui::segmented-control name="quiet" multiple size="sm" aria-labelledby="quiet-days-label" :options="['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun']" :value="['sat', 'sun']" />
                    </div>
                </form>
                <div class="flex justify-end"><x-gui::button color="ink" shape="pill" type="submit" form="notifications-form">Save</x-gui::button></div>
            </div>
        </x-gui::tabs.panel>

        <x-gui::tabs.panel name="account" class="space-y-6">
            <x-gui::card title="Appearance" description="This one works: it changes the theme of this page." variant="filled">
                <x-gui::theme-switcher />
            </x-gui::card>

            <x-gui::card title="Danger zone" variant="filled" class="ring-1 ring-gui-danger-text/40">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <p class="max-w-md text-sm text-zinc-600 dark:text-zinc-400">Delete your account and everything in it. This can't be undone.</p>
                    <x-gui::button color="danger" variant="outline" class="rounded-full!" commandfor="delete-account" command="show-modal">Delete account</x-gui::button>
                </div>
            </x-gui::card>
        </x-gui::tabs.panel>
    </x-gui::tabs>

    <x-slot:footer>
        <x-gui::modal.close>Done</x-gui::modal.close>
    </x-slot:footer>
</x-gui::modal>

{{-- Asked over the settings: a native dialog stacks on top of the one below. --}}
<x-gui::modal id="delete-account" title="Delete your account?" description="Everything in Acme goes with it, for good." size="sm">
    <x-slot:footer>
        <x-gui::modal.close>Cancel</x-gui::modal.close>
        <x-gui::button color="danger" commandfor="delete-account" command="close" x-on:click="GiraffeUI.toast('Not really: this is only an example.', { color: 'danger' })">Delete account</x-gui::button>
    </x-slot:footer>
</x-gui::modal>
