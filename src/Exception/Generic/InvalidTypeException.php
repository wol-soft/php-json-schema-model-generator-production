<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidTypeException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidTypeException extends ValidationException
{
    /**
     * PatternException constructor.
     *
     * @param $providedValue
     * @param array|string $expectedType
     */
    public function __construct($providedValue, string $propertyName, string $jsonPointer, protected $expectedType)
    {
        parent::__construct(
            sprintf(
                "Invalid type for '%s': requires %s, got '%s'",
                $propertyName,
                is_array($this->expectedType)
                    ? '[' . MessageFormatter::quotedList($this->expectedType) . ']'
                    : "'{$this->expectedType}'",
                is_object($providedValue) ? $providedValue::class : gettype($providedValue)
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
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
