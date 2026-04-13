<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * The JSON Pointer within the JSON Schema
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class JsonPointer
{
    public function __construct(public string $pointer)
    {}
}
