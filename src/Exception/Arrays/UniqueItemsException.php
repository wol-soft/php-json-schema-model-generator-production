<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class UniqueItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class UniqueItemsException extends ValidationException
{
    /**
     * UniqueItemsException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     */
    public function __construct($providedValue, string $propertyName)
    {
        parent::__construct(
            "Items of array $propertyName are not unique",
            $propertyName,
            $providedValue
        );
    }
}
