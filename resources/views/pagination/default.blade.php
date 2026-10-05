{{--
    Laravel pagination view. Use it with $users->links('giraffeui::pagination.default'), <x-gui::pagination :paginator="$users" />,
    or everywhere with Paginator::defaultView('giraffeui::pagination.default').
--}}
@php
    $link = 'inline-flex h-9 min-w-9 items-center justify-center rounded-gui px-3 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-zinc-100';
    $current = 'inline-flex h-9 min-w-9 items-center justify-center rounded-gui bg-gui-primary px-3 text-sm font-medium text-gui-primary-foreground';
    $disabled = 'inline-flex h-9 min-w-9 cursor-not-allowed items-center justify-center rounded-gui px-3 text-sm font-medium text-zinc-400 dark:text-zinc-600';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('giraffeui::ui.pagination') }}" class="flex flex-wrap items-center justify-between gap-3">
        @if (method_exists($paginator, 'total'))
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                {{-- The numbers are emphasised inside the translated sentence; everything is escaped first. --}}
                @php($number = fn ($value) => '<span class="font-medium text-zinc-900 dark:text-zinc-100">'.e($value).'</span>')
                {!! strtr(e(__('giraffeui::ui.showing')), [':first' => $number($paginator->firstItem()), ':last' => $number($paginator->lastItem()), ':total' => $number($paginator->total())]) !!}
            </p>
        @endif

        <ul class="flex flex-wrap items-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $disabled }}" aria-disabled="true">{{ __('giraffeui::ui.previous') }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }}">{{ __('giraffeui::ui.previous') }}</a>
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
                                    <a href="{{ $url }}" class="{{ $link }}"><span class="sr-only">Page </span>{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            @endisset

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }}">{{ __('giraffeui::ui.next') }}</a>
                @else
                    <span class="{{ $disabled }}" aria-disabled="true">{{ __('giraffeui::ui.next') }}</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
