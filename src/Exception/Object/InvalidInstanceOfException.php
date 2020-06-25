<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidInstanceOfException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidInstanceOfException extends ValidationException
{
    /** @var string */
    protected $expectedClass;

    /**
     * InvalidInstanceOfException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string $expectedClass
     */
    public function __construct($providedValue, string $propertyName, $expectedClass)
    {
        $this->expectedClass= $expectedClass;

        parent::__construct(
            sprintf(
                'Invalid class for %s. Requires %s, got %s',
                $propertyName,
                $expectedClass,
                get_class($providedValue)
            ),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string
     */
    public function getExpectedClass(): string
    {
        return $this->expectedClass;
    }
}
