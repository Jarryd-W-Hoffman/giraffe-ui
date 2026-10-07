{{-- The Store example's mug, drawn in the chosen glaze. view: front, side, top or set. Decoration. --}}
<svg viewBox="0 0 200 200" class="{{ $size }} drop-shadow-xl" aria-hidden="true" x-bind:style="`--mug: ${glazes[glaze]}`" style="--mug: #9cb79a">
    @if ($view === 'top')
        <ellipse cx="100" cy="100" rx="70" ry="70" fill="var(--mug)" />
        <ellipse cx="100" cy="100" rx="58" ry="58" fill="#3b2418" />
        <ellipse cx="88" cy="88" rx="18" ry="10" fill="#fff" opacity="0.12" />
        <path d="M168 92 C196 92 196 128 166 124" fill="none" stroke="var(--mug)" stroke-width="12" stroke-linecap="round" />
    @elseif ($view === 'set')
        @foreach ([[-34, 0.86], [30, 1]] as [$shift, $scale])
            <g transform="translate({{ 100 + $shift }} 104) scale({{ $scale }}) translate(-100 -100)">
                <path d="M138 78 C170 78 170 128 136 124" fill="none" stroke="var(--mug)" stroke-width="12" stroke-linecap="round" />
                <path d="M58 60 H142 V140 C142 156 128 164 112 164 H88 C72 164 58 156 58 140 Z" fill="var(--mug)" />
                <ellipse cx="100" cy="60" rx="42" ry="10" fill="#000" opacity="0.18" />
                <path d="M66 70 V138" stroke="#fff" stroke-width="6" stroke-linecap="round" opacity="0.2" />
            </g>
        @endforeach
    @else
        @php($flip = $view === 'side')
        <g @if ($flip) transform="translate(200 0) scale(-1 1)" @endif>
            <path d="M138 78 C176 78 176 132 136 128" fill="none" stroke="var(--mug)" stroke-width="14" stroke-linecap="round" />
            <path d="M54 52 H146 V142 C146 160 130 170 112 170 H88 C70 170 54 160 54 142 Z" fill="var(--mug)" />
            <ellipse cx="100" cy="52" rx="46" ry="11" fill="#000" opacity="0.18" />
            <path d="M64 64 V140" stroke="#fff" stroke-width="7" stroke-linecap="round" opacity="0.2" />
            <path d="M54 150 C70 168 130 168 146 150" fill="none" stroke="#000" stroke-width="3" opacity="0.08" />
        </g>
    @endif
</svg>
