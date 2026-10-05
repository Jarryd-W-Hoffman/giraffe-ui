{{-- A row whose table.select checkbox is ticked is highlighted with :has(), no JavaScript needed. --}}
<tr {{ $attributes->class('has-[[data-gui-select]:checked]:bg-gui-primary/5') }}>{{ $slot }}</tr>
