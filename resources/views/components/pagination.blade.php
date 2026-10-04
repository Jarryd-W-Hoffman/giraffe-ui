@props([
    'paginator',
    'livewire' => false,
    'window' => 2,
])

{{-- $paginator->links() works with both length-aware and simple paginators. --}}
<div {{ $attributes }}>
    {{ $paginator->onEachSide($window)->links($livewire ? 'giraffeui::pagination.livewire' : 'giraffeui::pagination.default') }}
</div>
