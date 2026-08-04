<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Thrown when an array contains items at indices not evaluated by any sibling positive
 * applicator and the schema's `unevaluatedItems` keyword is `false`.
 */
class UnevaluatedItemsException extends ValidationException
{
    /**
     * @param int[] $unevaluatedItems Zero-based indices of the offending array entries.
     */
    public function __construct(
        mixed $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $unevaluatedItems,
    ) {
        $formattedIndices = implode(
            ', ',
            array_map(
                static fn(int $index): string => '#' . $index,
                $unevaluatedItems,
            ),
        );

        parent::__construct(
            "Provided JSON for '$propertyName' contains not allowed unevaluated items "
                . "[$formattedIndices]",
            $propertyName,
            $providedValue,
            $jsonPointer,
        );
    }

    /**
     * @return int[]
     */
    public function getUnevaluatedItems(): array
    {
        return $this->unevaluatedItems;
    }
}
