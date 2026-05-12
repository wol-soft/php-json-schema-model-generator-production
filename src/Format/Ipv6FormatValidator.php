<?php

declare(strict_types=1);

namespace PHPModelGenerator\Format;

class Ipv6FormatValidator implements FormatValidatorInterface
{
    public static function validate(string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }
}
