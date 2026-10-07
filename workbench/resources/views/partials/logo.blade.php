{{-- GiraffeUI's mark: a U whose right arm is a giraffe's neck. Decorative: say "GiraffeUI" next to it. --}}
@php($logoId = 'gui-logo-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6)))
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true" class="{{ $class ?? 'size-8' }}">
  <defs>
    <linearGradient id="{{ $logoId }}-u" x1="0" y1="0" x2="0.4" y2="1">
      <stop offset="0" stop-color="#ffd60a"/>
      <stop offset="1" stop-color="#f7a600"/>
    </linearGradient>
    <linearGradient id="{{ $logoId }}-head" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#ffcf1f"/>
      <stop offset="1" stop-color="#eda10c"/>
    </linearGradient>
    <mask id="{{ $logoId }}-neck">
      <path d="M287 425 A92 92 0 0 0 348 338 V200" fill="none" stroke="#fff" stroke-width="84"/>
    </mask>
  </defs>
  <path d="M164 176 V338 A92 92 0 0 0 290 424" fill="none" stroke="url(#{{ $logoId }}-u)" stroke-width="84" stroke-linecap="round"/>
  <g mask="url(#{{ $logoId }}-neck)">
    <rect x="200" y="150" width="240" height="320" fill="#fff4dc"/>
    <g fill="#d98a14">
      <path d="M312 206 h36 l8 26 l-30 14 l-18 -14 z"/>
      <path d="M362 214 h24 v46 l-22 -6 l-6 -22 z"/>
      <path d="M306 258 l26 6 l6 34 l-28 10 l-14 -26 z"/>
      <path d="M346 270 l40 -4 v44 l-30 6 l-14 -20 z"/>
      <path d="M298 326 l30 -6 l14 26 l-12 30 l-34 -6 z"/>
      <path d="M354 330 l32 -2 l-4 44 l-30 4 l-8 -26 z"/>
      <path d="M266 384 l34 -2 l8 34 l-26 22 l-30 -18 z"/>
      <path d="M322 394 l30 -4 l-6 36 l-30 12 l-6 -22 z"/>
      <path d="M232 404 l22 6 l-4 34 l-30 -8 z"/>
    </g>
    <path d="M384 200 V338 A128 128 0 0 1 340 436" fill="none" stroke="#5a3207" stroke-width="12"/>
  </g>
  <g fill="#b06d06" transform="translate(5 4)">
    <rect x="318" y="84" width="20" height="56" rx="10" transform="rotate(-12 328 112)"/>
    <rect x="348" y="80" width="20" height="58" rx="10" transform="rotate(6 358 109)"/>
  </g>
  <g fill="#f2b20f">
    <rect x="318" y="84" width="20" height="56" rx="10" transform="rotate(-12 328 112)"/>
    <rect x="348" y="80" width="20" height="58" rx="10" transform="rotate(6 358 109)"/>
  </g>
  <circle cx="322" cy="88" r="13" fill="#e39a0a"/>
  <circle cx="361" cy="84" r="13" fill="#e39a0a"/>
  <path d="M372 150 C392 128 420 124 436 128 C428 150 404 170 380 172 Z" fill="#f4b417"/>
  <path d="M380 156 C396 142 414 138 424 138 C416 152 400 162 386 164 Z" fill="#7a4a0a"/>
  <path d="M390 236 C392 196 392 152 368 130 C348 112 312 112 286 126 C264 138 246 152 226 160 C204 169 196 190 206 204 C214 216 232 220 252 218 C272 216 290 216 304 222 C306 236 306 246 306 258 C330 244 356 236 390 236 Z" fill="#b06d06" transform="translate(4 9)"/>
  <path d="M390 236 C392 196 392 152 368 130 C348 112 312 112 286 126 C264 138 246 152 226 160 C204 169 196 190 206 204 C214 216 232 220 252 218 C272 216 290 216 304 222 C306 236 306 246 306 258 C330 244 356 236 390 236 Z" fill="url(#{{ $logoId }}-head)"/>
  <path d="M312 170 C312 150 338 140 356 152 C366 160 364 178 352 188 C338 198 314 192 312 170 Z" fill="#fff4dc"/>
  <path d="M322 164 Q336 154 350 162" fill="none" stroke="#2a1606" stroke-width="7" stroke-linecap="round"/>
  <ellipse cx="220" cy="180" rx="7" ry="5" fill="#5a3207" transform="rotate(-25 220 180)"/>
  <path d="M214 204 Q236 214 262 210" fill="none" stroke="#c07a0c" stroke-width="5" stroke-linecap="round"/>
</svg>
