<?php

namespace JayAitch\GiraffeUI\Support;

use Illuminate\Support\Str;

/**
 * CSS anchor positioning for popovers, dropdowns and tooltips (Baseline since January 2026).
 *
 * Classes are written out in full so Tailwind finds them; giraffeui.css adds src/ as a @source.
 */
class Anchor
{
    /**
     * A valid CSS anchor name for an element id.
     */
    public static function name(string $id): string
    {
        return '--gui-'.(Str::slug($id) ?: 'anchor');
    }

    /**
     * Placement classes. The browser flips to the other side when there isn't room.
     */
    public static function position(string $position): string
    {
        return match ($position) {
            'bottom' => '[position-area:bottom] my-1',
            'bottom-end' => '[position-area:bottom_span-left] my-1',
            'top' => '[position-area:top] my-1',
            'top-start' => '[position-area:top_span-right] my-1',
            'top-end' => '[position-area:top_span-left] my-1',
            'left' => '[position-area:left] mx-1',
            'right' => '[position-area:right] mx-1',
            default => '[position-area:bottom_span-right] my-1',
        }.' [position-try-fallbacks:flip-block,flip-inline]';
    }
}
