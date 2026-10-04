<?php

namespace JayAitch\GiraffeUI\Support;

use Illuminate\Support\Str;
use Livewire\Livewire;
use WeakMap;

/**
 * Ids for elements that weren't given one.
 *
 * Inside a Livewire component they're stable across renders: the component's own id plus a counter. A random id
 * would change on every update, and Livewire's morph would then replace the element, losing focus and Alpine state.
 */
class Id
{
    /**
     * @var WeakMap<object, int>|null
     */
    protected static ?WeakMap $counts = null;

    public static function make(string $prefix): string
    {
        $component = class_exists(Livewire::class) ? Livewire::current() : null;

        // current() is false outside a component render.
        if (! is_object($component) || ! method_exists($component, 'getId')) {
            return $prefix.'-'.Str::lower(Str::random(8));
        }

        self::$counts ??= new WeakMap;
        $count = self::$counts[$component] = (self::$counts[$component] ?? 0) + 1;

        // Each request hydrates a fresh component, so the count starts again and repeats the same ids.
        return $prefix.'-'.substr(hash('xxh32', $component->getId()), 0, 6).'-'.$count;
    }
}
