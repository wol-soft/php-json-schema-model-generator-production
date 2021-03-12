<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidPatternPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidPatternPropertiesException extends ValidationException
{
    /** @var ValidationException[][] */
    protected $nestedExceptions;
    /** @var string */
    protected $pattern;

    /**
     * InvalidAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param ValidationException[][] $nestedExceptions
     */
    public function __construct($providedValue, string $propertyName, string $pattern, $nestedExceptions)
    {
        $this->nestedExceptions = $nestedExceptions;
        $this->pattern = $pattern;

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
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

    /**
     * @return string
     */
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
                        array_map(function (ValidationException $exception): string {
                            return $exception->getMessage();
                        }, $exceptions)
                    )
                )
            );
        }

        return "Provided JSON for $propertyName contains invalid pattern properties." . $output;
    }
}
