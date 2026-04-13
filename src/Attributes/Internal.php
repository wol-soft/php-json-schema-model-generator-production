<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * Marks a property as internal to the model infrastructure.
 * Properties with this attribute are excluded from serialization.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Internal
{
    public function __construct()
    {}
}
