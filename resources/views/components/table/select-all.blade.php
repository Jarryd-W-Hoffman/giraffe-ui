@props([
    'label' => 'Select all rows',
])

{{--
    A header checkbox for the table.select checkboxes in the body: checked when all are, mixed when some are.
    Clicking it ticks or clears every row, firing input and change on each so wire:model and x-model follow.
    It keeps up as rows change (pagination, Livewire updates) and as scripts or wire:model tick them.
--}}
<th
    scope="col"
    class="w-px px-4 py-3"
    x-data="{
        rows() {
            return [...this.$el.closest('table').querySelectorAll('tbody [data-gui-select]')].filter((box) => ! box.disabled);
        },
        sync() {
            const rows = this.rows();
            const ticked = rows.filter((box) => box.checked).length;
            this.$refs.box.checked = rows.length > 0 && ticked === rows.length;
            this.$refs.box.indeterminate = ticked > 0 && ticked < rows.length;
            this.$refs.box.disabled = rows.length === 0;
        },
        toggle() {
            const checked = this.$refs.box.checked;
            for (const box of this.rows()) {
                if (box.checked === checked) continue;
                box.checked = checked;
                box.dispatchEvent(new Event('input', { bubbles: true }));
                box.dispatchEvent(new Event('change', { bubbles: true }));
            }
            this.sync();
        },
        init() {
            const table = this.$el.closest('table');
            table.addEventListener('change', (event) => event.target.matches?.('[data-gui-select]') && this.sync());
            table.addEventListener('gui-select-sync', () => this.sync());
            new MutationObserver(() => this.sync()).observe(table.tBodies[0], { childList: true, subtree: true });
            this.sync();
        },
    }"
>
    <input
        type="checkbox"
        x-ref="box"
        aria-label="{{ $label }}"
        x-on:change="toggle()"
        {{ $attributes->class('block size-4 shrink-0 cursor-pointer rounded-sm accent-gui-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text disabled:cursor-not-allowed disabled:opacity-50 dark:scheme-dark') }}
    >
</th>
