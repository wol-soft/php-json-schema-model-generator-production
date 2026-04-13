<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class MinItemsException extends ValidationException
{
    /**
     * MinItemsException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $minItems)
    {
        parent::__construct(
            "Array $propertyName must not contain less than {$this->minItems} items",
            $propertyName,
            $providedValue
        );
    }

    public function getMinItems(): int
    {
        return $this->minItems;
    }
}
