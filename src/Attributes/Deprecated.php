<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * Marks a generated class or property as deprecated in the JSON Schema
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class Deprecated
{
    public function __construct()
    {}
}
