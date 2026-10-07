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
     * Inline style that pins an absolutely positioned part (a button or icon) to one side of its control.
     *
     * Classes like max-w-xs on the control then move the part with it. Browsers without anchor positioning drop
     * these declarations and fall back to the part's own classes (e.g. end-0), which fit a full-width control.
     */
    public static function pin(string $id, string $side, string $inset = '0px'): string
    {
        $name = self::name($id);
        [$property, $edge] = in_array($side, ['left', 'start'], true) ? ['inset-inline-start', 'start'] : ['inset-inline-end', 'end'];

        return "position-anchor: {$name}; top: calc(anchor(top) + {$inset}); bottom: calc(anchor(bottom) + {$inset}); {$property}: calc(anchor({$edge}) + {$inset})";
    }

    /**
     * Placement classes. The browser flips to the other side when there isn't room.
     */
    public static function position(string $position): string
    {
        return match ($position) {
            'bottom' => '[position-area:bottom] my-1',
            'bottom-end' => '[position-area:bottom_span-x-start] my-1',
            'top' => '[position-area:top] my-1',
            'top-start' => '[position-area:top_span-x-end] my-1',
            'top-end' => '[position-area:top_span-x-start] my-1',
            'left' => '[position-area:left] mx-1',
            'right' => '[position-area:right] mx-1',
            default => '[position-area:bottom_span-x-end] my-1',
        }.' [position-try-fallbacks:flip-block,flip-inline]';
    }
}
