<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidAdditionalPropertiesException extends ValidationException
{
    protected const MAIN_MESSAGE = "Provided JSON for '%s' contains invalid additional properties";
    protected const TYPE = 'additional property';

    /**
     * InvalidAdditionalPropertiesException constructor.
     *
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

        // This exception validates the enclosing object's own additionalProperties/items
        // constraint. $propertyName is the generated class name (a base validator has no access
        // to the property name its parent used to reach this object) — a message-text label only,
        // never a real instance-path segment.
        $this->suppressOwnInstancePointerSegment();
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
                implode(
                    "\n    * ",
                    str_replace(
                        "\n",
                        "\n    ",
                        array_map(fn(ValidationException $exception): string => $exception->getMessage(), $exceptions)
                    )
                )
            );
        }

        return sprintf(static::MAIN_MESSAGE, $propertyName) . $output;
    }
}
