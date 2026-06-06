<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Format;

class UriFormatValidator implements FormatValidatorInterface
{
    public static function validate(?string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_URL) !== false;
    }
}
