@extends('catalog::examples.layout')

@section('content')
    <main class="flex min-h-dvh items-center justify-center bg-zinc-50 px-4 py-12 dark:bg-zinc-950">
        <div class="w-full max-w-sm">
            <div class="mb-6 text-center">
                <span class="inline-flex size-10 items-center justify-center rounded-gui bg-gui-primary font-semibold text-gui-primary-foreground" aria-hidden="true">A</span>
                <h1 class="mt-4 text-xl font-semibold tracking-tight">Sign in to Acme</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    New here? <a href="#" class="font-medium text-gui-primary-text hover:underline">Create an account</a>
                </p>
            </div>

            <x-gui::card>
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
                        <div x-data="{ show: false }">
                            <x-gui::input x-bind:type="show ? 'text' : 'password'" type="password" autocomplete="current-password">
                                <x-slot:trailing>
                                    <button type="button" x-on:click="show = ! show" x-bind:aria-pressed="show.toString()" aria-pressed="false" class="cursor-pointer rounded-sm text-xs font-medium hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100">
                                        Show<span class="sr-only"> password</span>
                                    </button>
                                </x-slot:trailing>
                            </x-gui::input>
                        </div>
                    </x-gui::field>

                    <div class="flex items-center justify-between gap-4">
                        <x-gui::checkbox name="remember" label="Remember me" />
                        <a href="#" class="text-sm font-medium text-gui-primary-text hover:underline">Forgot password?</a>
                    </div>

                    <x-gui::button type="submit" class="w-full">Sign in</x-gui::button>
                </form>
            </x-gui::card>

            <p class="mt-6 text-center text-xs text-zinc-600 dark:text-zinc-400">
                By signing in you agree to the <a href="#" class="underline hover:text-zinc-900 dark:hover:text-zinc-100">terms</a> and <a href="#" class="underline hover:text-zinc-900 dark:hover:text-zinc-100">privacy policy</a>.
            </p>
        </div>
    </main>
@endsection
