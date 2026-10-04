<?php

namespace JayAitch\GiraffeUI\Support;

use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;

/**
 * Shared logic for form components, so a field and its control always agree on ids and errors.
 */
class Form
{
    /**
     * Derive a stable element id from a field name, e.g. "address[city]" becomes "gui-address-city".
     */
    public static function id(?string $name, ?string $suffix = null): ?string
    {
        if ($name === null || $name === '') {
            return null;
        }

        $id = Str::slug(trim(str_replace(['[', ']', '.'], '-', $name.($suffix !== null ? '-'.$suffix : '')), '-'));

        return $id === '' ? null : 'gui-'.$id;
    }

    /**
     * Convert a field name to the key Laravel uses for its errors, e.g. "address[city]" becomes "address.city".
     */
    public static function errorKey(?string $name, ?ComponentAttributeBag $attributes = null): ?string
    {
        if ($name !== null && $name !== '') {
            return trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
        }

        // Livewire inputs often have wire:model but no name.
        return $attributes?->whereStartsWith('wire:model')->first();
    }

    /**
     * Get the error message for a field, preferring one passed in explicitly.
     */
    public static function error(?string $key, mixed $error = null, mixed $errors = null): ?string
    {
        if (is_string($error) && $error !== '') {
            return $error;
        }

        if ($key !== null && $errors instanceof ViewErrorBag && $errors->has($key)) {
            return $errors->first($key);
        }

        return null;
    }

    /**
     * Build an aria-describedby value from the ids that exist, keeping any the developer passed.
     */
    public static function describedBy(?string $existing, ?string ...$ids): ?string
    {
        $value = trim(implode(' ', array_filter([$existing, ...$ids])));

        return $value === '' ? null : $value;
    }

    /**
     * Work out the attributes for a control (input, textarea, select), inside a Field or on its own.
     *
     * The control's own attributes always win over what the field provides.
     *
     * @param  array{id: ?string, name: ?string, hint: bool, error: ?string, required: bool}|null  $field
     * @return array{attributes: array<string, mixed>, name: ?string, errorKey: ?string, invalid: bool}
     */
    public static function control(?array $field, ComponentAttributeBag $attributes, mixed $errors, ?bool $invalid = null): array
    {
        $name = $attributes->get('name', $field['name'] ?? null);
        $id = $attributes->get('id', $field['id'] ?? self::id($name));
        $errorKey = self::errorKey($name, $attributes);
        $message = self::error($errorKey, $field['error'] ?? null, $errors);
        $invalid ??= $message !== null;

        return [
            'attributes' => array_filter([
                'id' => $id,
                'name' => $name,
                'required' => $attributes->has('required') ? null : (($field['required'] ?? false) ?: null),
                'aria-invalid' => $invalid ? 'true' : null,
                'aria-describedby' => self::describedBy(
                    $attributes->get('aria-describedby'),
                    $field && $field['hint'] && $id ? "{$id}-hint" : null,
                    $field && $message !== null && $id ? "{$id}-error" : null,
                ),
            ], fn ($value) => $value !== null),
            'name' => $name,
            'errorKey' => $errorKey,
            'invalid' => $invalid,
        ];
    }

    /**
     * Get the value to show in a control, falling back to old input after a failed form submission.
     */
    public static function value(ComponentAttributeBag $attributes, ?string $errorKey, ?string $type = null): mixed
    {
        if ($attributes->has('value')) {
            return $attributes->get('value');
        }

        // Livewire sets the value itself, and passwords and files should never be refilled.
        if ($errorKey === null || $attributes->whereStartsWith('wire:model')->isNotEmpty() || in_array($type, ['password', 'file'], true)) {
            return null;
        }

        return old($errorKey);
    }
}
