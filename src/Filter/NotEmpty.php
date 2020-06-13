<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

/**
 * Class NotEmpty
 *
 * @package PHPModelGenerator\Filter
 */
class NotEmpty
{
    public static function filter(?array $value): ?array
    {
        return $value !== null ? array_values(array_filter($value)) : null;
    }
}
