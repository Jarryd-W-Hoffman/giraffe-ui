@props([
    'variant' => 'glow',
])

{{--
    Decoration for behind a hero, hidden from screen readers. It fills its section from behind: give the section
    relative isolate overflow-hidden.
      glow       a soft lime-to-violet glow over a fine grid (the default)
      grid       the fine grid on its own, fading out from the top
      dots       a dotted pattern, fading out from the centre
      aurora     drifting blurred colour, with no pattern
      spotlight  a pool of light from the top centre
--}}
@php($variant = in_array($variant, ['glow', 'grid', 'dots', 'aurora', 'spotlight'], true) ? $variant : 'glow')

<div {{ $attributes->class('pointer-events-none absolute inset-0 -z-10 overflow-hidden') }} aria-hidden="true">
    @if ($variant === 'glow' || $variant === 'aurora')
        @if ($variant === 'aurora')
            <div class="absolute -top-24 left-[10%] h-96 w-96 rounded-full bg-lime-300/40 blur-3xl dark:bg-lime-400/15"></div>
            <div class="absolute top-0 right-[8%] h-[28rem] w-[28rem] rounded-full bg-violet-400/35 blur-3xl dark:bg-violet-600/25"></div>
            <div class="absolute top-40 left-1/3 h-80 w-[36rem] rounded-full bg-sky-300/30 blur-3xl dark:bg-sky-500/15"></div>
        @else
            <div class="absolute top-0 left-1/2 h-[36rem] w-[64rem] -translate-x-1/2 rounded-full bg-linear-to-r from-lime-300/40 via-violet-400/30 to-violet-600/40 opacity-70 blur-3xl dark:from-lime-400/20 dark:via-violet-500/20 dark:to-violet-700/30"></div>
        @endif
    @endif
    @if ($variant === 'glow' || $variant === 'grid')
        <div class="absolute inset-0 bg-[linear-gradient(to_right,var(--color-zinc-200)_1px,transparent_1px),linear-gradient(to_bottom,var(--color-zinc-200)_1px,transparent_1px)] bg-size-[3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_at_top,black_30%,transparent_70%)] opacity-60 dark:bg-[linear-gradient(to_right,var(--color-zinc-800)_1px,transparent_1px),linear-gradient(to_bottom,var(--color-zinc-800)_1px,transparent_1px)]"></div>
    @endif
    @if ($variant === 'dots')
        <div class="absolute inset-0 bg-[radial-gradient(var(--color-zinc-300)_1px,transparent_1px)] bg-size-[1.25rem_1.25rem] [mask-image:radial-gradient(ellipse_at_center,black_20%,transparent_70%)] dark:bg-[radial-gradient(var(--color-zinc-700)_1px,transparent_1px)]"></div>
    @endif
    @if ($variant === 'spotlight')
        <div class="absolute -top-40 left-1/2 h-[40rem] w-[56rem] -translate-x-1/2 bg-[radial-gradient(ellipse_at_top,color-mix(in_oklab,var(--color-violet-400)_35%,transparent),transparent_65%)] dark:bg-[radial-gradient(ellipse_at_top,color-mix(in_oklab,var(--color-violet-500)_30%,transparent),transparent_65%)]"></div>
        <div class="absolute top-0 left-1/2 h-px w-2/3 -translate-x-1/2 bg-linear-to-r from-transparent via-violet-400/60 to-transparent"></div>
    @endif
</div>
