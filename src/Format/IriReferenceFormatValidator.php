<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class IriReferenceFormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        // An IRI-reference is either an absolute IRI or a relative reference allowing Unicode.
        if (IriFormatValidator::validate($input)) {
            return true;
        }

        // Relative IRI-reference: encode non-ASCII then validate as URI-reference
        $encoded = preg_replace_callback(
            '/[^\x00-\x7F]+/',
            static fn(array $m): string => rawurlencode($m[0]),
            $input,
        );

        return $encoded !== null && UriReferenceFormatValidator::validate($encoded);
    }
}
