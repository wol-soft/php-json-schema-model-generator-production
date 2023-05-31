<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

use BackedEnum;

class Enum
{
    public static function filter($value, $options): ?BackedEnum
    {
        return $value === null ? null : $options['fqcn']::from($value);
    }

    public static function serialize(?BackedEnum $value): null | int | string
    {
        return $value?->value;
    }
}
