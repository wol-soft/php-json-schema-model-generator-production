<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * The original JSON name from the schema, the code was generated from
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class SchemaName
{
    public function __construct(public string $name)
    {}
}
