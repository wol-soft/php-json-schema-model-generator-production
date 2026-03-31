<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class UriTemplateFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        // RFC 6570 URI Template: no spaces and all braces must be properly closed
        if (preg_match('/\s/', $input)) {
            return false;
        }

        // Validate that each { is properly closed by } and expressions are non-empty
        return preg_match('/\{[^{}]*\}|[^{}]/', $input) !== false
            && !preg_match('/\{[^}]*$|^[^{]*\}/', $input);
    }
}
