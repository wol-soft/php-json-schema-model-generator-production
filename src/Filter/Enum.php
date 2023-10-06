<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

use UnitEnum;

class Enum
{
    public static function filter($value, $options): ?UnitEnum
    {
        return $value === null ? null : $options['fqcn']::from($value);
    }

    public static function serialize(?UnitEnum $value): null | int | string
    {
        return $value?->value;
    }
}
