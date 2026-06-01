<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

/**
 * Provides access to the raw model input without exposing it as a public method on the model class.
 *
 * A reference to the backing array is held so that changes made through the model
 * (e.g. property setters that update $_rawModelDataInput) are immediately visible.
 */
class Meta
{
    public function __construct(private array &$rawModelDataInput) {}

    public function rawInput(): array
    {
        return $this->rawModelDataInput;
    }
}
