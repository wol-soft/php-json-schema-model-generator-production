<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Thrown when an unevaluated item's value fails the `unevaluatedItems: <schema>` subschema.
 *
 * Reuses InvalidItemException's per-index error aggregation; only the message phrasing
 * differs, so the spec keyword that triggered the rejection is visible in the output.
 */
class InvalidUnevaluatedItemsException extends InvalidItemException
{
    protected function getErrorMessage(string $propertyName): string
    {
        $output = "Invalid unevaluated items in array $propertyName:";
        foreach ($this->getInvalidItems() as $itemIndex => $exceptions) {
            $output .= "\n  - invalid unevaluated item #$itemIndex\n    * " .
                implode(
                    "\n    * ",
                    str_replace(
                        "\n",
                        "\n    ",
                        array_map(
                            static fn(ValidationException $exception): string => $exception->getMessage(),
                            $exceptions,
                        ),
                    ),
                );
        }

        return $output;
    }
}
