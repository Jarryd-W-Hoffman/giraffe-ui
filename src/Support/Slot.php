<?php

namespace JayAitch\GiraffeUI\Support;

use Illuminate\Contracts\Support\Htmlable;

class Slot
{
    /**
     * Whether a slot has no real content.
     *
     * Livewire wraps @if and @foreach blocks in <!--[if BLOCK]><![endif]--> markers, so a slot with a loop
     * that rendered nothing isn't "empty" to Blade. Ignore those markers and whitespace.
     */
    public static function isBlank(Htmlable|string|null $slot): bool
    {
        if ($slot === null) {
            return true;
        }

        $html = $slot instanceof Htmlable ? $slot->toHtml() : $slot;

        return trim(preg_replace('/<!--\[if (?:END)?BLOCK\]><!\[endif\]-->/', '', $html)) === '';
    }
}
