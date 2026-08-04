<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\MessageFormatter;

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
        $output = "Invalid unevaluated items in array '$propertyName':";
        foreach ($this->getInvalidItems() as $itemIndex => $exceptions) {
            $output .= "\n  - invalid unevaluated item #$itemIndex\n    * "
                . MessageFormatter::bulletList($exceptions);
        }

        return $output;
    }
}
