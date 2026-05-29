<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

use InvalidArgumentException;
use PHPModelGenerator\ValueObject\ImmutableMediaString as ImmutableMediaStringValue;
use PHPModelGenerator\ValueObject\MediaString;

class ImmutableMediaString extends AbstractMediaStringFilter
{
    /**
     * Wrap a raw string in an ImmutableMediaString carrying the schema-defined media type and
     * encoding.
     *
     * Pass-through rules:
     *   - null                 → null (nullable property)
     *   - ImmutableMediaString → returned unchanged after validating mediaType/encoding match
     *   - MediaString          → converted to ImmutableMediaString after validating mediaType/encoding match
     *   - string               → wrapped with mediaType / encoding from $options
     *
     * @param string|MediaString|ImmutableMediaStringValue|null $value
     * @param array{mediaType?: string|null, encoding?: string|null} $options
     *
     * @throws InvalidArgumentException when a pre-existing wrapper's mediaType or encoding
     *                                  does not match the schema-declared values
     */
    public static function filter(
        string|MediaString|ImmutableMediaStringValue|null $value,
        array $options = [],
    ): ?ImmutableMediaStringValue {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ImmutableMediaStringValue || $value instanceof MediaString) {
            self::assertMetadataMatch($value->getMediaType(), $value->getEncoding(), $options);

            if ($value instanceof ImmutableMediaStringValue) {
                return $value;
            }

            return new ImmutableMediaStringValue(
                $value->getValue(),
                $options['mediaType'] ?? null,
                $options['encoding'] ?? null,
            );
        }

        return new ImmutableMediaStringValue(
            $value,
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
