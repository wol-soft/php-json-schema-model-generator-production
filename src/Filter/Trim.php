<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

/**
 * Class Trim
 *
 * @package PHPModelGenerator\Filter
 */
class Trim
{
    public static function filter(?string $value): ?string
    {
        return $value !== null ? trim($value) : null;
    }
}
