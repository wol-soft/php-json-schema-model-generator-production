<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidPatternPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidPatternPropertiesException extends ValidationException
{
    /**
     * InvalidAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param ValidationException[][] $nestedExceptions
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected string $pattern,
        protected $nestedExceptions
    ) {
        foreach ($this->nestedExceptions as $nestedPropertyName => $exceptions) {
            foreach ($exceptions as $exception) {
                $exception->setInstancePointerParent($this, $nestedPropertyName);
            }
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);

        // This exception validates the enclosing object's own patternProperties constraint.
        // $propertyName is the generated class name (a base validator has no access to the
        // property name its parent used to reach this object) — a message-text label only, never
        // a real instance-path segment.
        $this->suppressOwnInstancePointerSegment();
    }

    /**
     * Get a list of nested exceptions containing all failed validations indexed by the required pattern
     *
     * @return ValidationException[][]
     */
    public function getNestedExceptions(): array
    {
        return $this->nestedExceptions;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = '';
        foreach ($this->nestedExceptions as $nestedPropertyName => $exceptions) {
            $output .= sprintf(
                "\n  - invalid property '%s' matching pattern '%s'\n    * %s",
                $nestedPropertyName,
                $this->pattern,
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

        return "Provided JSON for '$propertyName' contains invalid pattern properties" . $output;
    }
}
