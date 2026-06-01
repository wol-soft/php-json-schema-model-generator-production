<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

/**
 * Provides access to the raw model input without exposing it as a public method on the model class.
 */
class Meta
{
    public function __construct(private readonly \Closure $rawInputGetter) {}

    public function rawInput(): array
    {
        return ($this->rawInputGetter)();
    }
}
