<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

use PHPModelGenerator\ValueObject\ImmutableMediaString;
use PHPModelGenerator\ValueObject\MediaString as MediaStringValue;

/**
 * Filter and serializer callables for the MediaString value object.
 *
 * Separated from the value object to keep concerns clean: MediaString is a pure data holder,
 * while this class owns the transformation and serialization logic for the filter pipeline.
 */
class MediaString
{
    /**
     * Wrap a raw string in a MediaString carrying the schema-defined media type and encoding.
     *
     * Pass-through rules:
     *   - null   → null (nullable property)
     *   - MediaString          → returned unchanged (already transformed)
     *   - ImmutableMediaString → converted to MediaString preserving the raw value
     *   - string               → wrapped with mediaType / encoding from $options
     *
     * @param string|MediaStringValue|ImmutableMediaString|null $value
     * @param array{mediaType?: string|null, encoding?: string|null} $options
     */
    public static function filter(
        string|MediaStringValue|ImmutableMediaString|null $value,
        array $options = [],
    ): ?MediaStringValue {
        if ($value === null) {
            return null;
        }

        if ($value instanceof MediaStringValue) {
            return $value;
        }

        $rawValue = $value instanceof ImmutableMediaString ? $value->getValue() : $value;

        return new MediaStringValue($rawValue, $options['mediaType'] ?? null, $options['encoding'] ?? null);
    }

    /**
     * Serialize a MediaString back to its raw string value.
     */
    public static function serialize(MediaStringValue|null $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }
}
