<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidAdditionalPropertiesException extends ValidationException
{
    protected const MAIN_MESSAGE = 'Provided JSON for %s contains invalid additional properties.';
    protected const TYPE = 'additional property';

    /**
     * InvalidAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param ValidationException[][] $nestedExceptions
     */
    public function __construct($providedValue, string $propertyName, protected $nestedExceptions)
    {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
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
