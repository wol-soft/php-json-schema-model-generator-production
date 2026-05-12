<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

use InvalidArgumentException;
use PHPModelGenerator\ValueObject\ImmutableMediaString;
use PHPModelGenerator\ValueObject\MediaString as MediaStringValue;

class MediaString extends AbstractMediaStringFilter
{
    /**
     * Wrap a raw string in a MediaString carrying the schema-defined media type and encoding.
     *
     * Pass-through rules:
     *   - null                 → null (nullable property)
     *   - MediaString          → returned unchanged after validating mediaType/encoding match
     *   - ImmutableMediaString → converted to MediaString after validating mediaType/encoding match
     *   - string               → wrapped with mediaType / encoding from $options
     *
     * @param string|MediaStringValue|ImmutableMediaString|null $value
     * @param array{mediaType?: string|null, encoding?: string|null} $options
     *
     * @throws InvalidArgumentException when a pre-existing wrapper's mediaType or encoding
     *                                  does not match the schema-declared values
     */
    public static function filter(
        string|MediaStringValue|ImmutableMediaString|null $value,
        array $options = [],
    ): ?MediaStringValue {
        if ($value === null) {
            return null;
        }

        if ($value instanceof MediaStringValue || $value instanceof ImmutableMediaString) {
            self::assertMetadataMatch($value->getMediaType(), $value->getEncoding(), $options);

            if ($value instanceof MediaStringValue) {
                return $value;
            }

            return new MediaStringValue($value->getValue(), $options['mediaType'] ?? null, $options['encoding'] ?? null);
        }

        return new MediaStringValue($value, $options['mediaType'] ?? null, $options['encoding'] ?? null);
    }

    /**
     * Serialize a MediaString back to its raw string value.
     */
    public static function serialize(MediaStringValue|null $value): ?string
    {
        return $value !== null ? (string) $value : null;
    }
}
