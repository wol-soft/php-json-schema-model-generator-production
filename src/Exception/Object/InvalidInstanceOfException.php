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
    public function __construct($providedValue, string $propertyName, protected $expectedClass)
    {
        parent::__construct(
            sprintf(
                'Invalid class for %s. Requires %s, got %s',
                $propertyName,
                $this->expectedClass,
                $providedValue::class
            ),
            $propertyName,
            $providedValue
        );
    }

    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }
}
