@extends('catalog::examples.layout')

@section('content')
    @php
        $glazes = [
            'sage' => ['label' => 'Sage', 'color' => '#9cb79a'],
            'clay' => ['label' => 'Clay', 'color' => '#d0835f'],
            'ink' => ['label' => 'Ink', 'color' => '#2f3b55'],
            'cream' => ['label' => 'Cream', 'color' => '#ece3cf'],
        ];
        $views = ['front' => 'from the front', 'side' => 'from the side', 'top' => 'from above', 'set' => 'as a pair'];
        $reviews = [
            ['Grace Hopper', 5, '2 days ago', 'Holds a proper amount of coffee and stays warm. The sage is even nicer in person.'],
            ['Alan Turing', 4, 'Last week', 'Lovely weight and a comfortable handle. I wish it came in a bigger size.'],
            ['Katherine Johnson', 5, 'September', 'Bought two in ink. They have survived the dishwasher every day for a month.'],
        ];
        $related = [
            ['Pour-over set', '$58', '#c9a27e'],
            ['Espresso cups', '$32', '#7d8fa6'],
            ['Tea caddy', '$24', '#a6b48f'],
            ['Serving plate', '$46', '#d9c9b0'],
        ];
    @endphp

    <div class="min-h-dvh bg-white dark:bg-black" x-data="{ glaze: 'sage', glazes: @js(collect($glazes)->map(fn ($g) => $g['color'])), view: 'front', cart: 0 }">
        <x-gui::navbar sticky variant="pills" container="max-w-7xl" class="border-transparent! bg-white/90! dark:bg-black/90!">
            <x-slot:brand>
                <x-example::brand />
            </x-slot:brand>
            <x-gui::navbar.item href="#">Kitchen</x-gui::navbar.item>
            <x-gui::navbar.item href="/catalog/examples/store">Tableware</x-gui::navbar.item>
            <x-gui::navbar.item href="#">Gifts</x-gui::navbar.item>
            <x-slot:actions>
                <x-gui::button variant="ghost" color="ink" shape="pill" icon aria-label="Search the shop"><x-gui::icon name="magnifying-glass" size="sm" /></x-gui::button>
                <x-gui::button variant="ghost" color="ink" shape="pill" icon class="relative" x-bind:aria-label="'Cart, ' + cart + (cart === 1 ? ' item' : ' items')" aria-label="Cart, 0 items">
                    <x-gui::icon name="shopping-bag" size="sm" />
                    <span x-show="cart > 0" x-cloak x-text="cart" class="absolute -end-1 -top-1 flex size-5 items-center justify-center rounded-full bg-gui-primary text-[11px] font-semibold text-gui-primary-foreground"></span>
                </x-gui::button>
            </x-slot:actions>
        </x-gui::navbar>

        <main class="mx-auto max-w-7xl space-y-20 px-4 pt-6 pb-16 sm:px-6">
            <x-gui::breadcrumbs :items="['Shop' => '#', 'Tableware' => '#', 'Everyday mug' => null]" />

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-16">
                {{-- The gallery: one picture at a time, chosen by the thumbnails. The glaze recolours every one. --}}
                <section aria-label="Pictures" class="space-y-4">
                    <div class="relative aspect-square overflow-hidden rounded-3xl bg-zinc-100 dark:bg-zinc-900" role="img" x-bind:aria-label="'The everyday mug in ' + glaze + ', ' + @js($views)[view]" aria-label="The everyday mug in sage, from the front">
                        @foreach ($views as $key => $text)
                            <div class="absolute inset-0 flex items-center justify-center" x-show="view === '{{ $key }}'" @if ($key !== 'front') x-cloak @endif>
                                @include('catalog::examples.partials.mug', ['view' => $key, 'size' => 'w-3/5'])
                            </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-4 gap-3" role="radiogroup" aria-label="Choose a picture">
                        @foreach ($views as $key => $text)
                            <label class="cursor-pointer">
                                <input type="radio" name="store_view" value="{{ $key }}" x-model="view" class="peer sr-only">
                                <span class="sr-only">{{ ucfirst($text) }}</span>
                                <span class="flex aspect-square items-center justify-center rounded-2xl bg-zinc-100 ring-2 ring-transparent transition peer-checked:ring-zinc-900 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-gui-primary-text dark:bg-zinc-900 dark:peer-checked:ring-white" aria-hidden="true">
                                    @include('catalog::examples.partials.mug', ['view' => $key, 'size' => 'w-1/2'])
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section aria-labelledby="product-heading" class="space-y-8">
                    <div>
                        <x-gui::badge shape="pill" dot="success">In stock, ships tomorrow</x-gui::badge>
                        <h1 id="product-heading" class="mt-4 text-4xl font-normal tracking-tight sm:text-5xl">Everyday <span class="text-zinc-500 dark:text-zinc-400">mug</span></h1>
                        <div class="mt-4 flex items-center gap-3">
                            <x-gui::rating readonly :value="4.7" />
                            <a href="#reviews" class="text-sm text-zinc-600 underline-offset-2 hover:underline dark:text-zinc-400">128 reviews</a>
                        </div>
                        <p class="mt-6 text-3xl font-semibold tracking-tight tabular-nums">$28</p>
                        <p class="mt-4 max-w-prose text-zinc-600 dark:text-zinc-400">Thrown in stoneware and dipped by hand, so no two glazes are quite the same. Holds 350 ml, and it's happy in the dishwasher and microwave.</p>
                    </div>

                    <form class="space-y-6" x-on:submit.prevent="cart += Number(new FormData($el).get('quantity') || 1); GiraffeUI.toast('Added to your cart', { color: 'success' })">
                        <x-gui::swatches name="glaze" label="Glaze" value="sage" x-model="glaze" :options="$glazes" />

                        <div class="space-y-3">
                            <p id="size-label" class="text-sm font-medium">Size</p>
                            <div style="--radius-gui: 9999px">
                                <x-gui::segmented-control name="size" aria-labelledby="size-label" :options="['small' => '250 ml', 'regular' => '350 ml', 'large' => '450 ml']" value="regular" />
                            </div>
                        </div>

                        <div class="flex flex-wrap items-end gap-3">
                            <x-gui::field label="Quantity" name="quantity" class="w-36">
                                <x-gui::number-input :value="1" min="1" max="12" />
                            </x-gui::field>
                            <x-gui::button type="submit" color="ink" shape="pill" size="lg" class="flex-1">Add to cart</x-gui::button>
                            <x-gui::button variant="outline" color="ink" shape="pill" icon size="lg" aria-label="Save for later" x-on:click="GiraffeUI.toast('Saved for later')"><x-gui::icon name="heart" size="sm" /></x-gui::button>
                        </div>
                    </form>

                    <x-gui::accordion>
                        <x-gui::accordion.item title="Details" open>Stoneware, fired to 1,240°C. 9.5 cm tall and 8.5 cm across. Each one is signed on the base.</x-gui::accordion.item>
                        <x-gui::accordion.item title="Shipping">Free over $50. Ordered by 2 pm, it leaves the studio the next working day, wrapped in recycled paper.</x-gui::accordion.item>
                        <x-gui::accordion.item title="Returns">Not quite right? Send it back within 30 days for a refund. If it arrives broken, we'll send another.</x-gui::accordion.item>
                    </x-gui::accordion>
                </section>
            </div>

            {{-- Reviews. --}}
            <section id="reviews" aria-labelledby="reviews-heading" class="grid grid-cols-1 gap-10 lg:grid-cols-[20rem_minmax(0,1fr)]">
                <div>
                    <h2 id="reviews-heading" class="text-3xl font-normal tracking-tight">What people <span class="text-zinc-500 dark:text-zinc-400">say</span></h2>
                    <div class="mt-6 flex items-center gap-3">
                        <span class="text-5xl font-normal tracking-tight tabular-nums">4.7</span>
                        <div>
                            <x-gui::rating readonly :value="4.7" size="sm" />
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">from 128 reviews</p>
                        </div>
                    </div>
                    <div class="mt-6 space-y-2">
                        @foreach ([5 => 82, 4 => 31, 3 => 9, 2 => 4, 1 => 2] as $stars => $count)
                            <x-gui::progress :value="$count" :max="128" :label="$stars.' stars, '.$count.' reviews'" />
                        @endforeach
                    </div>
                    <x-gui::button variant="outline" color="ink" shape="pill" class="mt-6" commandfor="write-review" command="show-modal">Write a review</x-gui::button>
                </div>
                <ul class="space-y-4">
                    @foreach ($reviews as [$name, $stars, $when, $text])
                        <li class="rounded-3xl bg-zinc-100 p-6 dark:bg-zinc-900">
                            <div class="flex items-center gap-3">
                                <x-gui::avatar :name="$name" size="sm" />
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium">{{ $name }}</p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $when }}</p>
                                </div>
                                <x-gui::rating readonly :value="$stars" size="sm" />
                            </div>
                            <p class="mt-4 leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $text }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- More from the studio. --}}
            <section aria-labelledby="related-heading">
                <h2 id="related-heading" class="text-3xl font-normal tracking-tight">You might <span class="text-zinc-500 dark:text-zinc-400">also like</span></h2>
                <ul class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @foreach ($related as [$name, $price, $color])
                        <li>
                            <a href="#" class="group block rounded-3xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text">
                                <span class="flex aspect-square items-center justify-center rounded-3xl bg-zinc-100 transition group-hover:bg-zinc-200/70 dark:bg-zinc-900 dark:group-hover:bg-zinc-800/80" aria-hidden="true">
                                    <span class="size-1/3 rounded-full shadow-inner transition-transform group-hover:scale-110 motion-reduce:transition-none" style="background: {{ $color }}"></span>
                                </span>
                                <span class="mt-3 flex items-center justify-between gap-2 px-1">
                                    <span class="font-medium">{{ $name }}</span>
                                    <span class="text-zinc-600 tabular-nums dark:text-zinc-400">{{ $price }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </main>

        <x-gui::footer variant="simple" container="max-w-7xl" :links="['Shop' => ['Kitchen' => '#', 'Tableware' => '#', 'Gifts' => '#'], 'Help' => ['Shipping' => '#', 'Returns' => '#']]" class="border-t border-zinc-200 dark:border-zinc-800">
            <x-slot:brand><x-example::brand /></x-slot:brand>
            <x-slot:legal>© 2026 Acme Studio</x-slot:legal>
        </x-gui::footer>
    </div>

    <x-gui::modal id="write-review" title="Write a review" description="For the everyday mug." size="md">
        <form id="write-review-form" class="space-y-4" x-on:submit.prevent="$el.closest('dialog').close(); $el.reset(); GiraffeUI.toast('Thanks for your review!', { color: 'success' })">
            <x-gui::field label="Your rating" name="review_rating" required>
                <x-gui::rating />
            </x-gui::field>
            <x-gui::field label="Your review" name="review_text" required>
                <x-gui::textarea rows="4" autosize counter maxlength="500" />
            </x-gui::field>
        </form>
        <x-slot:footer>
            <x-gui::modal.close>Cancel</x-gui::modal.close>
            <x-gui::button type="submit" form="write-review-form" color="ink" shape="pill">Post review</x-gui::button>
        </x-slot:footer>
    </x-gui::modal>
@endsection
