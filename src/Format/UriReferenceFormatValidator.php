<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Format;

class UriReferenceFormatValidator implements FormatValidatorInterface
{
    public static function validate(?string $input): bool
    {
        return preg_match(
            '/^(([^:\/?#]+:)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?)$/',
            $input
        ) === 1;
    }
}
