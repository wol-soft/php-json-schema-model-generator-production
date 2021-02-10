<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Format;

interface FormatValidatorInterface
{
    /**
     * Validate if the given $input is valid for the format
     *
     * @param string $input
     *
     * @return bool
     */
    public static function validate(string $input): bool;
}
