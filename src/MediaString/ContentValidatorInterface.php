<?php

declare(strict_types=1);

namespace PHPModelGenerator\MediaString;

interface ContentValidatorInterface
{
    public static function validate(string $value): void;
}
