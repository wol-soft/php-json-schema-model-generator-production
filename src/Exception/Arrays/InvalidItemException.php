<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

class InvalidItemException extends ValidationException
{
    /**
     * @param $providedValue
     * @param ValidationException[][] $invalidItems Indexed by the 0-based index of the item in the array. In
     *                                              contrast to InvalidTupleException the keys are used as is for
     *                                              the instance pointer
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $invalidItems
    ) {
        foreach ($this->invalidItems as $itemIndex => $exceptions) {
            foreach ($exceptions as $exception) {
                $exception->setInstancePointerParent($this, $itemIndex);
            }
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);
    }

    /**
     * @return ValidationException[][] Indexed by the 0-based index of the item in the array
     */
    public function getInvalidItems(): array
    {
        return $this->invalidItems;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $output = "Invalid items in array '$propertyName':";
        foreach ($this->invalidItems as $itemIndex => $exceptions) {
            $output .= "\n  - invalid item #$itemIndex\n    * " . MessageFormatter::bulletList($exceptions);
        }

        return $output;
    }
}
