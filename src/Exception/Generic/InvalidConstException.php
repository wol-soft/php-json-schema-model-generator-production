<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidConstException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidConstException extends ValidationException
{
    /** @var mixed */
    protected $expectedValue;

    /**
     * InvalidConstException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param mixed $expectedValue
     */
    public function __construct($providedValue, string $propertyName, $expectedValue)
    {
        $this->expectedValue= $expectedValue;

        parent::__construct(
            "Invalid value for $propertyName declined by const constraint",
            $propertyName,
            $providedValue
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
