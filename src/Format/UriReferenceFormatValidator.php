<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class UriReferenceFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        // Empty string is a valid URI-reference (refers to the current document)
        if ($input === '') {
            return true;
        }

        // Absolute URI is always a valid URI-reference
        if (filter_var($input, FILTER_VALIDATE_URL)) {
            return true;
        }

        // Relative reference: must not contain spaces, null bytes, or other invalid chars
        return preg_match('/^[^\x00-\x1F\x7F ]*$/u', $input) === 1;
    }
}
