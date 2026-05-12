<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class RegexFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        return @preg_match($input, '') !== false;
    }
}
