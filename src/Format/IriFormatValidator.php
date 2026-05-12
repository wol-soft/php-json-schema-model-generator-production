<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class IriFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        // An IRI (RFC 3987) extends URIs to allow Unicode characters.
        // Require a scheme followed by :// and a non-empty authority/path,
        // with no control characters or unencoded spaces.
        return preg_match('/^[a-zA-Z][a-zA-Z0-9+\-.]*:\/\/.+/us', $input) === 1
            && !preg_match('/[\x00-\x1F\x7F ]/', $input);
    }
}
