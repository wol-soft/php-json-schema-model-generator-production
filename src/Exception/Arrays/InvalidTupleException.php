<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

class InvalidTupleException extends ValidationException
{
    /**
     * @param $providedValue
     * @param ValidationException[][] $invalidTuples Indexed by the 1-based position of the tuple element in
     *                                               the array (the first element has the key 1)
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $invalidTuples
    ) {
        foreach ($this->invalidTuples as $tuplePosition => $exceptions) {
            foreach ($exceptions as $exception) {
                // The keys are 1-based positions (also used for the "#<position>" label of the message) whereas an
                // instance pointer addresses the 0-based array index. Passing the key as is would point at the
                // following element.
                $exception->setInstancePointerParent($this, $tuplePosition - 1);
            }
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);
    }

    /**
     * @return ValidationException[][] Indexed by the 1-based position of the tuple element in the array
     */
    public function getInvalidTuples(): array
    {
        return $this->invalidTuples;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = "Invalid tuple item in array '$propertyName':";
        foreach ($this->invalidTuples as $tuplePosition => $exceptions) {
            $output .= "\n  - invalid tuple #$tuplePosition\n    * " . MessageFormatter::bulletList($exceptions);
        }

        return $output;
    }
}
