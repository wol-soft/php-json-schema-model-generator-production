<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

class MinItemsException extends ValidationException
{
    public function __construct($providedValue, string $propertyName, protected int $minItems, protected int $count)
    {
        parent::__construct(
            "Array $propertyName must not contain less than $this->minItems items, $this->count items provided",
            $propertyName,
            $providedValue
        );
    }

    public function getMinItems(): int
    {
        return $this->minItems;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
