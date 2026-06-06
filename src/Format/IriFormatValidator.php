<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Format;

class IriFormatValidator implements FormatValidatorInterface
{
    public static function validate(?string $input): bool
    {
        return preg_match(
            '/^[a-zA-Z][a-zA-Z0-9+\-.]*:[^\s]*$/u',
            $input
        ) === 1;
    }
}
