@extends('catalog::examples.layout')

@section('content')
    <x-example::auth-shell title="Create your Acme account">
        <x-slot:subtitle>
            Already have one? <a href="/catalog/examples/sign-in" class="font-medium text-gui-primary-text hover:underline">Sign in</a>
        </x-slot:subtitle>

        @if ($errors->any())
            <x-gui::alert color="danger" title="Almost there" class="mb-5">Fix the {{ $errors->count() === 1 ? 'field' : $errors->count().' fields' }} below to create your account.</x-gui::alert>
        @endif

        {{-- novalidate so the example shows the server's errors; without it the browser checks required fields first. --}}
        <form method="POST" action="/catalog/examples/register" class="space-y-5" novalidate>
            @csrf

            <x-gui::field label="Name" name="name" required>
                <x-gui::input autocomplete="name" placeholder="Ada Lovelace" />
            </x-gui::field>

            <x-gui::field label="Work email" name="email" required>
                <x-gui::input type="email" autocomplete="email" placeholder="ada@example.com" />
            </x-gui::field>

            <x-gui::field label="Password" name="password" hint="At least 8 characters, with a number." required>
                <x-gui::input type="password" viewable autocomplete="new-password" />
            </x-gui::field>

            <x-gui::field label="Confirm password" name="password_confirmation" required>
                <x-gui::input type="password" viewable autocomplete="new-password" />
            </x-gui::field>

            <x-gui::fieldset name="terms">
                <x-gui::checkbox name="terms" value="1" :checked="(bool) old('terms')" :aria-describedby="$errors->has('terms') ? 'gui-terms-fieldset-error' : null">
                    I agree to the <a href="#" class="font-medium text-gui-primary-text underline">terms</a> and <a href="#" class="font-medium text-gui-primary-text underline">privacy policy</a>
                </x-gui::checkbox>
            </x-gui::fieldset>

            <x-gui::button color="ink" shape="pill" type="submit" class="w-full">Create account</x-gui::button>
        </form>

        <x-slot:footer>
            Free for 14 days, then pick a plan. No card needed.
        </x-slot:footer>
    </x-example::auth-shell>
@endsection
