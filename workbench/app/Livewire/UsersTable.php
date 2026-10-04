<?php

namespace Workbench\App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortBy = 'name';

    #[Url]
    public string $sortDirection = 'asc';

    public function sort(string $column): void
    {
        $this->sortDirection = $this->sortBy === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function users(): LengthAwarePaginator
    {
        // Stands in for an Eloquent query like User::where(...)->orderBy(...)->paginate(5).
        $users = collect(range(1, 23))
            ->map(fn (int $i) => [
                'id' => $i,
                'name' => ['Ada Lovelace', 'Grace Hopper', 'Alan Turing', 'Katherine Johnson', 'Margaret Hamilton', 'Edsger Dijkstra', 'Barbara Liskov', 'Donald Knuth'][$i % 8].' '.$i,
                'role' => ['Admin', 'Editor', 'Viewer'][$i % 3],
            ])
            ->when($this->search, fn ($users) => $users->filter(fn ($user) => str_contains(strtolower($user['name']), strtolower($this->search))))
            ->sortBy($this->sortBy, SORT_NATURAL, $this->sortDirection === 'desc')
            ->values();

        return new LengthAwarePaginator($users->forPage($this->getPage(), 5)->values(), $users->count(), 5, $this->getPage());
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div class="space-y-4">
                <x-gui::input type="search" wire:model.live.debounce.250ms="search" placeholder="Search people" aria-label="Search people" class="max-w-xs" />

                <x-gui::table :sort="$sortBy" :direction="$sortDirection">
                    <x-slot:head>
                        <x-gui::table.heading sortable="name" wire:click="sort('name')">Name</x-gui::table.heading>
                        <x-gui::table.heading sortable="role" wire:click="sort('role')">Role</x-gui::table.heading>
                    </x-slot:head>

                    @foreach ($this->users() as $user)
                        <x-gui::table.row wire:key="user-{{ $user['id'] }}">
                            <x-gui::table.cell>{{ $user['name'] }}</x-gui::table.cell>
                            <x-gui::table.cell><x-gui::badge size="sm">{{ $user['role'] }}</x-gui::badge></x-gui::table.cell>
                        </x-gui::table.row>
                    @endforeach

                    <x-slot:empty>Nobody matches "{{ $search }}".</x-slot:empty>

                    <x-slot:footer>
                        <x-gui::pagination :paginator="$this->users()" livewire />
                    </x-slot:footer>
                </x-gui::table>
            </div>
            BLADE;
    }
}
