@extends('catalog::examples.layout')

@section('content')
    <x-example::auth-shell title="Sign in to Acme">
        <x-slot:subtitle>
            New here? <a href="/catalog/examples/register" class="font-medium text-gui-primary-text hover:underline">Create an account</a>
        </x-slot:subtitle>

        @if ($errors->any())
            <x-gui::alert color="danger" title="That didn't work" class="mb-5">Check the fields below and try again.</x-gui::alert>
        @endif

        {{-- novalidate so the example shows the server's errors; without it the browser checks required fields first. --}}
        <form method="POST" action="/catalog/examples/sign-in" class="space-y-5" novalidate>
            @csrf

            <x-gui::field label="Email" name="email" required>
                <x-gui::input type="email" autocomplete="email" placeholder="ada@example.com" />
            </x-gui::field>

            <x-gui::field label="Password" name="password" hint="Any 8 characters will do here." required>
                <x-gui::input type="password" viewable autocomplete="current-password" />
            </x-gui::field>

            <div class="flex items-center justify-between gap-4">
                <x-gui::checkbox name="remember" label="Remember me" />
                <a href="/catalog/examples/forgot-password" class="text-sm font-medium text-gui-primary-text hover:underline">Forgot password?</a>
            </div>

            <x-gui::button type="submit" class="w-full">Sign in</x-gui::button>
        </form>

        <x-slot:footer>
            By signing in you agree to the <a href="#" class="underline hover:text-zinc-900 dark:hover:text-zinc-100">terms</a> and <a href="#" class="underline hover:text-zinc-900 dark:hover:text-zinc-100">privacy policy</a>.
        </x-slot:footer>
    </x-example::auth-shell>
@endsection
