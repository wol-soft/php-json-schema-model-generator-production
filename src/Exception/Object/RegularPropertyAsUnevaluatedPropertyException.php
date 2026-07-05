<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Thrown when a key already declared in `properties` or matched by `patternProperties` at the
 * same schema level is routed through the unevaluatedProperties accessor's set() method.
 * Such keys belong to their own (typed) named setter or to the pattern-properties accessor;
 * accepting them via setUnevaluatedProperty would silently bypass the appropriate validator.
 */
class RegularPropertyAsUnevaluatedPropertyException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        private readonly string $class,
    ) {
        parent::__construct(
            "Couldn't add regular property $propertyName as unevaluated property to object {$this->class}",
            $propertyName,
            $providedValue,
            $jsonPointer,
        );
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
