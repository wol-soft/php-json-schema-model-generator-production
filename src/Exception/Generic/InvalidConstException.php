<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;
use PHPModelGenerator\Exception\ValueFormatter;

/**
 * Class InvalidConstException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidConstException extends ValidationException
{
    /**
     * InvalidConstException constructor.
     *
     * @param $providedValue
     * @param mixed $expectedValue
     */
    public function __construct($providedValue, string $propertyName, string $jsonPointer, protected $expectedValue)
    {
        parent::__construct(
            sprintf(
                "Value for '%s' must be %s, got %s",
                $propertyName,
                ValueFormatter::format($this->expectedValue),
                ValueFormatter::format($providedValue),
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    /**
     * @return mixed
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }
}
