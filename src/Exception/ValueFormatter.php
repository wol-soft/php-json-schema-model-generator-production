<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception;

/**
 * Renders a value for embedding in a validation exception message. Every value handled here
 * (const/enum values, provided input) originates from decoded JSON, so JSON notation is used
 * uniformly for scalars, arrays and objects alike, rather than PHP's var_export/print_r syntax.
 */
class ValueFormatter
{
    public static function format(mixed $value): string
    {
        $encoded = json_encode($value);

        return $encoded !== false ? $encoded : var_export($value, true);
    }
}
