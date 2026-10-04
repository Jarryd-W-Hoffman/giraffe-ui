<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use JayAitch\GiraffeUI\Tests\TestCase;

uses(TestCase::class)->in('Feature');

/**
 * Get the class names on the first matching element, as an array.
 */
function classesOf(string $html, string $tag): array
{
    preg_match('/<'.$tag.'\b[^>]*\bclass="([^"]*)"/s', $html, $matches);

    return preg_split('/\s+/', trim($matches[1] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * Share validation errors with every view, the way Laravel's ShareErrorsFromSession middleware does.
 */
function withErrors(array $messages): void
{
    view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag($messages)));
}

/**
 * Get one attribute from the first matching element.
 */
function attr(string $html, string $tag, string $attribute): ?string
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8"?>'.$html, LIBXML_NOERROR);
    $element = $dom->getElementsByTagName($tag)->item(0);

    return $element?->hasAttribute($attribute) ? $element->getAttribute($attribute) : null;
}
