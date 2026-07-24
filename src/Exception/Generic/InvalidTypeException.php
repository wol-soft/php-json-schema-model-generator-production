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
                self::describeProvidedValueType($providedValue)
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    /**
     * A JSON object and a JSON array both decode to a plain PHP array via json_decode($x, true),
     * so gettype() alone can't tell them apart - it reports 'array' for both, making a message
     * like "requires 'array', got 'array'" useless when a JSON object was actually provided.
     * array_is_list() is the same signal already used at every array/object type guard in the
     * generator; a non-list array is treated as a JSON object here for the same reason. An empty
     * array satisfies array_is_list() and is still reported as 'array' - the same accepted
     * {} vs [] limitation that applies at the type-guard level.
     */
    private static function describeProvidedValueType(mixed $providedValue): string
    {
        if (is_object($providedValue)) {
            return $providedValue::class;
        }

        if (is_array($providedValue) && !array_is_list($providedValue)) {
            return 'object';
        }

        return gettype($providedValue);
    }

    /**
     * @return array|string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }
}
