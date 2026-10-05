<?php

namespace Workbench\App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The Customers example: a data table with search, filters, sorting, pagination, row selection,
 * bulk actions and row actions. Changes live in the component, standing in for a database.
 */
class CustomersTable extends Component
{
    use WithPagination;

    public const PLANS = ['starter' => 'Starter', 'pro' => 'Pro', 'enterprise' => 'Enterprise'];

    public const STATUSES = ['active' => 'Active', 'trial' => 'Trial', 'churned' => 'Churned'];

    public const PER_PAGE = 10;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $plan = '';

    #[Url]
    public string $sortBy = 'name';

    #[Url]
    public string $sortDirection = 'asc';

    public const COLUMNS = ['plan' => 'Plan', 'status' => 'Status', 'mrr' => 'MRR', 'joined' => 'Joined'];

    /** @var list<string> The optional columns to show, toggled from the Columns menu. */
    public array $columns = ['plan', 'status', 'mrr', 'joined'];

    /** @var list<string> Ids of the selected customers, across pages. */
    public array $selected = [];

    /** @var list<string> Ids on the current page, so the select-all checkbox can work in the browser. */
    #[Locked]
    public array $pageIds = [];

    /** @var list<string> Ids waiting for the delete confirmation. */
    #[Locked]
    public array $deleting = [];

    /** @var list<string> */
    #[Locked]
    public array $deleted = [];

    /** @var array<string, string> id => status, for customers whose status changed. */
    #[Locked]
    public array $statuses = [];

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'plan'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['name', 'mrr', 'joined'], true)) {
            return;
        }

        $this->sortDirection = $this->sortBy === $column && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'plan');
        $this->resetPage();
    }

    public function setStatus(string $status, ?string $id = null): void
    {
        if (! isset(self::STATUSES[$status])) {
            return;
        }

        $ids = $this->existing($id === null ? $this->selected : [$id]);

        foreach ($ids as $each) {
            $this->statuses[$each] = $status;
        }

        $this->dispatch('toast', message: $this->describe($ids).' marked '.strtolower(self::STATUSES[$status]).'.', color: 'success');
    }

    /**
     * Ask before deleting one customer, or the selected ones.
     */
    public function confirmDelete(?string $id = null): void
    {
        $this->deleting = $this->existing($id === null ? $this->selected : [$id]);

        if ($this->deleting) {
            $this->js("document.getElementById('confirm-delete').showModal()");
        }
    }

    public function delete(): void
    {
        $ids = $this->existing($this->deleting);

        if (! $ids) {
            return;
        }

        $message = $this->describe($ids).' deleted.';
        $this->deleted = [...$this->deleted, ...$ids];
        $this->selected = array_values(array_diff($this->selected, $ids));
        $this->deleting = [];

        // The menu item that opened the dialog is gone, so put focus somewhere sensible.
        $this->js("document.getElementById('confirm-delete').close(); document.getElementById('customers-heading').focus()");
        $this->dispatch('toast', message: $message, color: 'success');
    }

    /**
     * Every customer that hasn't been deleted, with status changes applied.
     *
     * @return Collection<int, array{id: string, name: string, email: string, plan: string, status: string, mrr: int, joined: string}>
     */
    public function customers(): Collection
    {
        $first = ['Ada', 'Grace', 'Alan', 'Katherine', 'Margaret', 'Edsger', 'Barbara', 'Donald', 'Radia', 'Tim', 'Frances', 'John', 'Hedy', 'Dennis'];
        $last = ['Lovelace', 'Hopper', 'Turing', 'Johnson', 'Hamilton', 'Dijkstra', 'Liskov', 'Knuth', 'Perlman', 'Berners-Lee', 'Allen', 'McCarthy', 'Lamarr', 'Ritchie'];
        $plans = array_keys(self::PLANS);

        return collect(range(1, 42))
            ->map(function (int $i) use ($first, $last, $plans) {
                $name = $first[$i % 14].' '.$last[($i * 5 + intdiv($i, 14)) % 14];
                $plan = $plans[$i % 3];
                $id = 'cus_'.(1000 + $i);
                $status = $this->statuses[$id] ?? ($i % 7 === 0 ? 'churned' : ($i % 5 === 0 ? 'trial' : 'active'));

                return [
                    'id' => $id,
                    'name' => $name,
                    'email' => strtolower(str_replace([' ', '-'], ['.', ''], $name)).$i.'@example.com',
                    'plan' => $plan,
                    'status' => $status,
                    'mrr' => $status === 'active' ? ['starter' => 29, 'pro' => 99, 'enterprise' => 499][$plan] * (1 + $i % 4) : 0,
                    'joined' => now()->startOfDay()->subDays($i * 9)->toDateString(),
                ];
            })
            ->reject(fn (array $customer) => in_array($customer['id'], $this->deleted, true))
            ->values();
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        // Stands in for an Eloquent query with where(), orderBy() and paginate().
        $rows = $this->customers()
            ->when($this->search !== '', fn ($rows) => $rows->filter(fn ($row) => str_contains(strtolower($row['name'].' '.$row['email']), strtolower(trim($this->search)))))
            ->when(isset(self::STATUSES[$this->status]), fn ($rows) => $rows->where('status', $this->status))
            ->when(isset(self::PLANS[$this->plan]), fn ($rows) => $rows->where('plan', $this->plan))
            ->sortBy($this->sortBy, SORT_NATURAL, $this->sortDirection === 'desc')
            ->values();

        return new LengthAwarePaginator($rows->forPage($this->getPage(), self::PER_PAGE)->values(), $rows->count(), self::PER_PAGE, $this->getPage());
    }

    /**
     * @param  list<string>  $ids
     * @return list<string>
     */
    protected function existing(array $ids): array
    {
        return array_values(array_intersect($this->customers()->pluck('id')->all(), $ids));
    }

    /**
     * @param  list<string>  $ids
     */
    protected function describe(array $ids): string
    {
        return count($ids) === 1
            ? $this->customers()->firstWhere('id', $ids[0])['name']
            : count($ids).' customers';
    }

    public function render(): string
    {
        $this->pageIds = $this->rows->pluck('id')->all();

        return <<<'BLADE'
            @php
                $rows = $this->rows;
                $filtered = $search !== '' || $status !== 'all' || $plan !== '';
                $statusColors = ['active' => 'success', 'trial' => 'info', 'churned' => 'secondary'];
                $deletingNames = $this->customers()->whereIn('id', $deleting)->pluck('name')->all();
            @endphp

            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <x-gui::input type="search" wire:model.live.debounce.250ms="search" placeholder="Search by name or email" aria-label="Search customers" class="w-full sm:max-w-xs">
                        <x-slot:leading>
                            <x-gui::icon name="magnifying-glass" size="sm" />
                        </x-slot:leading>
                    </x-gui::input>

                    <x-gui::segmented-control wire:model.live="status" label="Status" size="sm" :options="['all' => 'All'] + self::STATUSES" />

                    <x-gui::select wire:model.live="plan" aria-label="Plan" size="sm" :options="['' => 'All plans'] + self::PLANS" class="w-auto" />

                    @if ($filtered)
                        <x-gui::button variant="ghost" color="secondary" size="sm" wire:click="clearFilters">Clear filters</x-gui::button>
                    @endif

                    {{-- Checkbox items are real checkboxes, so wire:model works on them and the menu stays open. --}}
                    <div class="sm:ml-auto">
                        <x-gui::dropdown id="customer-columns" position="bottom-end">
                            <x-slot:trigger>
                                <x-gui::button size="sm" variant="outline" color="secondary" commandfor="customer-columns" command="toggle-popover" aria-haspopup="menu">
                                    Columns
                                    <x-slot:trailing>
                                        <x-gui::icon name="chevron-down" size="sm" />
                                    </x-slot:trailing>
                                </x-gui::button>
                            </x-slot:trigger>
                            <x-gui::dropdown.group heading="Show columns">
                                @foreach (self::COLUMNS as $column => $label)
                                    <x-gui::dropdown.checkbox wire:model.live="columns" value="{{ $column }}">{{ $label }}</x-gui::dropdown.checkbox>
                                @endforeach
                            </x-gui::dropdown.group>
                        </x-gui::dropdown>
                    </div>
                </div>

                {{-- Announces the selection, which otherwise only shows up visually in the bar below. --}}
                <p class="sr-only" aria-live="polite">{{ $selected ? count($selected).' selected' : '' }}</p>

                @if ($selected)
                    <div class="flex flex-wrap items-center gap-3 rounded-gui border border-gui-primary/30 bg-gui-primary/5 px-4 py-2.5 text-sm">
                        <span class="font-medium">{{ count($selected) }} selected</span>
                        <div class="flex flex-wrap gap-2 sm:ml-auto">
                            <x-gui::button size="sm" variant="outline" color="secondary" wire:click="setStatus('churned')">Mark churned</x-gui::button>
                            <x-gui::button size="sm" variant="outline" color="danger" wire:click="confirmDelete">Delete</x-gui::button>
                            <x-gui::button size="sm" variant="ghost" color="secondary" wire:click="$set('selected', [])">Clear selection</x-gui::button>
                        </div>
                    </div>
                @endif

                <x-gui::table label="Customers" :sort="$sortBy" :direction="$sortDirection">
                    <x-slot:head>
                        <x-gui::table.heading class="w-px">
                            {{-- Selects or clears the current page. The browser works out checked and mixed from the server's ids. --}}
                            <x-gui::checkbox
                                aria-label="Select all customers on this page"
                                x-effect="const on = $wire.pageIds.filter((id) => $wire.selected.includes(id)).length; $el.checked = on > 0 && on === $wire.pageIds.length; $el.indeterminate = on > 0 && on < $wire.pageIds.length"
                                x-on:change="$wire.set('selected', $el.checked ? [...new Set([...$wire.selected, ...$wire.pageIds])] : $wire.selected.filter((id) => ! $wire.pageIds.includes(id)))"
                                :disabled="$rows->isEmpty()"
                            />
                        </x-gui::table.heading>
                        <x-gui::table.heading sortable="name" wire:click="sort('name')">Customer</x-gui::table.heading>
                        @if (in_array('plan', $columns, true))
                            <x-gui::table.heading>Plan</x-gui::table.heading>
                        @endif
                        @if (in_array('status', $columns, true))
                            <x-gui::table.heading>Status</x-gui::table.heading>
                        @endif
                        @if (in_array('mrr', $columns, true))
                            <x-gui::table.heading sortable="mrr" wire:click="sort('mrr')" class="text-right">MRR</x-gui::table.heading>
                        @endif
                        @if (in_array('joined', $columns, true))
                            <x-gui::table.heading sortable="joined" wire:click="sort('joined')" class="hidden md:table-cell">Joined</x-gui::table.heading>
                        @endif
                        <x-gui::table.heading><span class="sr-only">Actions</span></x-gui::table.heading>
                    </x-slot:head>

                    @foreach ($rows as $customer)
                        <x-gui::table.row wire:key="{{ $customer['id'] }}" :class="in_array($customer['id'], $selected, true) ? 'bg-gui-primary/5' : null">
                            <x-gui::table.cell>
                                <x-gui::checkbox wire:model.live="selected" value="{{ $customer['id'] }}" aria-label="Select {{ $customer['name'] }}" />
                            </x-gui::table.cell>
                            <x-gui::table.cell>
                                <div class="flex items-center gap-3">
                                    <x-gui::avatar :name="$customer['name']" size="sm" />
                                    <div class="min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $customer['name'] }}</p>
                                        <p class="text-zinc-600 dark:text-zinc-400">{{ $customer['email'] }}</p>
                                    </div>
                                </div>
                            </x-gui::table.cell>
                            @if (in_array('plan', $columns, true))
                                <x-gui::table.cell>{{ self::PLANS[$customer['plan']] }}</x-gui::table.cell>
                            @endif
                            @if (in_array('status', $columns, true))
                                <x-gui::table.cell><x-gui::badge size="sm" :color="$statusColors[$customer['status']]">{{ self::STATUSES[$customer['status']] }}</x-gui::badge></x-gui::table.cell>
                            @endif
                            @if (in_array('mrr', $columns, true))
                                <x-gui::table.cell class="text-right tabular-nums">${{ number_format($customer['mrr']) }}</x-gui::table.cell>
                            @endif
                            @if (in_array('joined', $columns, true))
                                <x-gui::table.cell class="hidden md:table-cell">{{ \Illuminate\Support\Carbon::parse($customer['joined'])->format('j M Y') }}</x-gui::table.cell>
                            @endif
                            <x-gui::table.cell class="text-right leading-none">
                                <x-gui::dropdown id="actions-{{ $customer['id'] }}" position="bottom-end">
                                    <x-slot:trigger>
                                        <button type="button" commandfor="actions-{{ $customer['id'] }}" command="toggle-popover" aria-haspopup="menu" class="flex cursor-pointer rounded-gui p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                                            <x-gui::icon name="ellipsis-horizontal" label="Actions for {{ $customer['name'] }}" />
                                        </button>
                                    </x-slot:trigger>
                                    <x-gui::dropdown.item href="#">View profile</x-gui::dropdown.item>
                                    @if ($customer['status'] === 'churned')
                                        <x-gui::dropdown.item wire:click="setStatus('active', '{{ $customer['id'] }}')">Reactivate</x-gui::dropdown.item>
                                    @else
                                        <x-gui::dropdown.item wire:click="setStatus('churned', '{{ $customer['id'] }}')">Mark churned</x-gui::dropdown.item>
                                    @endif
                                    <x-gui::dropdown.separator />
                                    <x-gui::dropdown.item color="danger" wire:click="confirmDelete('{{ $customer['id'] }}')">Delete</x-gui::dropdown.item>
                                </x-gui::dropdown>
                            </x-gui::table.cell>
                        </x-gui::table.row>
                    @endforeach

                    <x-slot:empty>
                        <x-gui::empty-state title="No customers found" :description="$filtered ? 'Nobody matches these filters. Try another search, or clear them.' : 'Customers appear here once they sign up.'" class="py-4">
                            <x-slot:icon>
                                <x-gui::icon name="user" />
                            </x-slot:icon>
                            @if ($filtered)
                                <x-slot:actions>
                                    <x-gui::button size="sm" variant="outline" color="secondary" wire:click="clearFilters">Clear filters</x-gui::button>
                                </x-slot:actions>
                            @endif
                        </x-gui::empty-state>
                    </x-slot:empty>

                    @if ($rows->hasPages())
                        <x-slot:footer>
                            <x-gui::pagination :paginator="$rows" livewire />
                        </x-slot:footer>
                    @endif
                </x-gui::table>

                <x-gui::modal id="confirm-delete" :title="count($deletingNames) === 1 ? 'Delete '.$deletingNames[0].'?' : 'Delete '.count($deletingNames).' customers?'" :description="count($deletingNames) === 1 ? 'Their subscription ends now and their data is removed.' : 'Their subscriptions end now and their data is removed.'" size="sm">
                    <x-slot:footer>
                        <x-gui::modal.close>Cancel</x-gui::modal.close>
                        <x-gui::button color="danger" wire:click="delete">Delete</x-gui::button>
                    </x-slot:footer>
                </x-gui::modal>
            </div>
            BLADE;
    }
}
