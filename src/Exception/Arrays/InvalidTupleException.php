<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidTupleException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class InvalidTupleException extends ValidationException
{
    /**
     * InvalidTupleException constructor.
     *
     * @param $providedValue
     * @param ValidationException[][] $invalidTuples
     */
    public function __construct($providedValue, string $propertyName, protected array $invalidTuples)
    {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

    /**
     * @return ValidationException[][]
     */
    public function getInvalidTuples(): array
    {
        return $this->invalidTuples;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = "Invalid tuple item in array $propertyName:";
        foreach ($this->invalidTuples as $tupleIndex => $exceptions) {
            $output .= "\n  - invalid tuple #$tupleIndex\n    * " .
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
