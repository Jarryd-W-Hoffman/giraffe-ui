{{-- A password input with a Show button. Put it in a field like any control; other attributes go to the input. --}}
<div x-data="{ show: false }">
    <x-gui::input x-bind:type="show ? 'text' : 'password'" type="password" {{ $attributes }}>
        <x-slot:trailing>
            <button type="button" x-on:click="show = ! show" x-bind:aria-pressed="show.toString()" aria-pressed="false" class="cursor-pointer rounded-sm text-xs font-medium hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100">
                Show<span class="sr-only"> password</span>
            </button>
        </x-slot:trailing>
    </x-gui::input>
</div>
