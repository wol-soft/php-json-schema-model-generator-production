<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidInstanceOfException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidInstanceOfException extends ValidationException
{
    /**
     * InvalidInstanceOfException constructor.
     *
     * @param $providedValue
     * @param string $expectedClass
     */
    public function __construct($providedValue, string $propertyName, string $jsonPointer, protected $expectedClass)
    {
        parent::__construct(
            sprintf(
                "Invalid class for '%s': requires '%s', got '%s'",
                $propertyName,
                $this->expectedClass,
                $providedValue::class
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }
}
