<?php

declare(strict_types=1);

namespace PHPModelGenerator\Attributes;

use Attribute;

/**
 * The JSON Schema source file
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class Source
{
    public function __construct(public string $source)
    {}
}
