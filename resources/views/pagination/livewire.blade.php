{{--
    Livewire pagination view: buttons call Livewire's gotoPage, previousPage and nextPage.
    Use it with <x-gui::pagination :paginator="$users" livewire />, or return 'giraffeui::pagination.livewire'
    from your component's paginationView() method.
--}}
@php
    $link = 'inline-flex h-9 min-w-9 items-center justify-center rounded-gui px-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100';
    $current = 'inline-flex h-9 min-w-9 items-center justify-center rounded-gui bg-gui-primary px-3 text-sm font-medium text-gui-primary-foreground';
    $disabled = 'inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-gui px-3 text-sm font-medium text-zinc-400 dark:text-zinc-600';
@endphp

@if ($paginator->hasPages())
    <nav wire:key="gui-pagination-{{ $paginator->getPageName() }}" role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-between gap-3">
        @if (method_exists($paginator, 'total'))
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Showing <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $paginator->firstItem() }}</span>
                to <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $paginator->lastItem() }}</span>
                of <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $paginator->total() }}</span>
            </p>
        @endif

        <ul class="flex flex-wrap items-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $disabled }}" aria-disabled="true">Previous</span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" class="{{ $link }} cursor-pointer">Previous</button>
                @endif
            </li>

            @isset($elements)
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="hidden sm:block"><span class="{{ $disabled }}" aria-hidden="true">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li class="hidden sm:block">
                                @if ($page == $paginator->currentPage())
                                    <span class="{{ $current }}" aria-current="page"><span class="sr-only">Page </span>{{ $page }}</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="{{ $link }} cursor-pointer"><span class="sr-only">Page </span>{{ $page }}</button>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            @endisset

            <li>
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" class="{{ $link }} cursor-pointer">Next</button>
                @else
                    <span class="{{ $disabled }}" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
