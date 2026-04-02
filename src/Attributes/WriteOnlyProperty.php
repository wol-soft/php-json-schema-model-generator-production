<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * Marks a generated property as writeOnly in the JSON Schema
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class WriteOnlyProperty
{
    public function __construct()
    {}
}
