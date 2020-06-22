<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidTypeException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidTypeException extends ValidationException
{
    /** @var array|string */
    protected $expectedType;

    /**
     * PatternException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param array|string $expectedType
     */
    public function __construct($providedValue, string $propertyName, $expectedType)
    {
        $this->expectedType= $expectedType;

        parent::__construct(
            sprintf(
                'Invalid type for %s. Requires %s, got %s',
                $propertyName,
                is_array($expectedType) ? '[' . join(', ', $expectedType) . ']' : $expectedType,
                gettype($providedValue)),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return array|string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }
}
