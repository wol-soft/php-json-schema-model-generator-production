<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

use InvalidArgumentException;

abstract class AbstractMediaStringFilter
{
    /**
     * @param array{mediaType?: string|null, encoding?: string|null} $options
     *
     * @throws InvalidArgumentException
     */
    protected static function assertMetadataMatch(
        ?string $actualMediaType,
        ?string $actualEncoding,
        array $options,
    ): void {
        $expectedMediaType = $options['mediaType'] ?? null;
        $expectedEncoding = $options['encoding'] ?? null;

        if ($actualMediaType !== $expectedMediaType) {
            throw new InvalidArgumentException(sprintf(
                'MediaString mediaType mismatch: expected %s, got %s',
                $expectedMediaType ?? 'null',
                $actualMediaType ?? 'null',
            ));
        }

        if ($actualEncoding !== $expectedEncoding) {
            throw new InvalidArgumentException(sprintf(
                'MediaString encoding mismatch: expected %s, got %s',
                $expectedEncoding ?? 'null',
                $actualEncoding ?? 'null',
            ));
        }
    }
}
