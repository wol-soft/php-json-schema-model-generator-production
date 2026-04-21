<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

use PHPModelGenerator\ValueObject\ImmutableMediaString as ImmutableMediaStringValue;
use PHPModelGenerator\ValueObject\MediaString;

/**
 * Filter and serializer callables for the ImmutableMediaString value object.
 *
 * Separated from the value object to keep concerns clean: ImmutableMediaString is a pure data
 * holder, while this class owns the transformation and serialization logic for the filter pipeline.
 */
class ImmutableMediaString
{
    /**
     * Wrap a raw string in an ImmutableMediaString carrying the schema-defined media type and
     * encoding.
     *
     * Pass-through rules:
     *   - null                 → null (nullable property)
     *   - ImmutableMediaString → returned unchanged (already transformed)
     *   - MediaString          → converted to ImmutableMediaString preserving the raw value
     *   - string               → wrapped with mediaType / encoding from $options
     *
     * @param string|MediaString|ImmutableMediaStringValue|null $value
     * @param array{mediaType?: string|null, encoding?: string|null} $options
     */
    public static function filter(
        string|MediaString|ImmutableMediaStringValue|null $value,
        array $options = [],
    ): ?ImmutableMediaStringValue {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ImmutableMediaStringValue) {
            return $value;
        }

        $rawValue = $value instanceof MediaString ? $value->getValue() : $value;

        return new ImmutableMediaStringValue(
            $rawValue,
            $options['mediaType'] ?? null,
            $options['encoding'] ?? null,
        );
    }

    /**
     * Serialize an ImmutableMediaString back to its raw string value.
     */
    public static function serialize(ImmutableMediaStringValue|null $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }
}
