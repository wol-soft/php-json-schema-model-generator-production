<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * Marks a generated property as readOnly in the JSON Schema
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ReadOnlyProperty
{
    public function __construct()
    {}
}
