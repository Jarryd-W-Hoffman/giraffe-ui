@extends('catalog::examples.layout')

@section('content')
    <x-example::auth-shell title="Reset your password">
        <x-slot:subtitle>
            Enter your email and we'll send you a link to choose a new one.
        </x-slot:subtitle>

        @if (session('status'))
            <x-gui::alert color="success" title="Check your email" class="mb-5" role="status">{{ session('status') }}</x-gui::alert>
        @endif

        {{-- novalidate so the example shows the server's errors; without it the browser checks the email first. --}}
        <form method="POST" action="/catalog/examples/forgot-password" class="space-y-5" novalidate>
            @csrf

            <x-gui::field label="Email" name="email" required>
                <x-gui::input type="email" autocomplete="email" placeholder="ada@example.com" />
            </x-gui::field>

            <x-gui::button type="submit" class="w-full">{{ session('status') ? 'Send another link' : 'Send reset link' }}</x-gui::button>
        </form>

        <x-slot:footer>
            <a href="/catalog/examples/sign-in" class="inline-flex items-center gap-1 text-sm font-medium text-gui-primary-text hover:underline">
                <x-gui::icon name="arrow-left" size="sm" />
                Back to sign in
            </a>
        </x-slot:footer>
    </x-example::auth-shell>
@endsection
