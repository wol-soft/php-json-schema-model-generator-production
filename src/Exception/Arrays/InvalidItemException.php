<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidItemException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class InvalidItemException extends ValidationException
{
    /**
     * InvalidItemException constructor.
     *
     * @param $providedValue
     * @param ValidationException[][] $invalidItems
     */
    public function __construct($providedValue, string $propertyName, protected array $invalidItems)
    {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

    /**
     * @return ValidationException[][]
     */
    public function getInvalidItems(): array
    {
        return $this->invalidItems;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = "Invalid items in array $propertyName:";
        foreach ($this->invalidItems as $itemIndex => $exceptions) {
            $output .= "\n  - invalid item #$itemIndex\n    * " .
                implode(
                    "\n    * ",
                    str_replace(
                        "\n",
                        "\n    ",
                        array_map(fn(ValidationException $exception): string => $exception->getMessage(), $exceptions)
                    )
                );
        }

        return $output;
    }
}
