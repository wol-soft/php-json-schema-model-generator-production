<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class UriFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        // RFC 3986 absolute URI: must have a scheme and a non-empty authority/path
        if (!filter_var($input, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Reject scheme-only URIs (e.g. "http://") — host is required
        $parsed = parse_url($input);
        return isset($parsed['host']) && $parsed['host'] !== '';
    }
}
