<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

class InvalidAdditionalPropertiesException extends ValidationException
{
    protected const MAIN_MESSAGE = "Provided JSON for '%s' contains invalid additional properties";
    protected const TYPE = 'additional property';

    /**
     * Whether the property name handed to the constructor is a label for the message text only
     * instead of a real instance path segment. Subclasses validating a property which is reached
     * under its own name override this to keep the name as the exception's instance pointer segment.
     */
    protected const PROPERTY_NAME_IS_PLACEHOLDER = true;

    /**
     * @param $providedValue
     * @param ValidationException[][] $nestedExceptions
     */
    public function __construct($providedValue, string $propertyName, string $jsonPointer, protected $nestedExceptions)
    {
        foreach ($this->nestedExceptions as $nestedPropertyName => $exceptions) {
            foreach ($exceptions as $exception) {
                $exception->setInstancePointerParent($this, $nestedPropertyName);
            }
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);

        // For the additionalProperties constraint of an object $propertyName is the generated class
        // name (a base validator has no access to the property name its parent used to reach this
        // object) - a message-text label only, never a real instance path segment. Additional tuple
        // items are validated by the array property itself and keep the array's name instead.
        if (static::PROPERTY_NAME_IS_PLACEHOLDER) {
            $this->suppressOwnInstancePointerSegment();
        }
    }

    /**
     * Get a list of nested exceptions containing all failed validations indexed by the property name
     *
     * @return ValidationException[][]
     */
    public function getNestedExceptions(): array
    {
        return $this->nestedExceptions;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = '';
        foreach ($this->nestedExceptions as $nestedPropertyName => $exceptions) {
            $output .= sprintf(
                "\n  - invalid %s '%s'\n    * %s",
                static::TYPE,
                $nestedPropertyName,
                MessageFormatter::bulletList($exceptions),
            );
        }

        return sprintf(static::MAIN_MESSAGE, $propertyName) . $output;
    }
}
