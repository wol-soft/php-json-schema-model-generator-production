<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class MinItemsException extends ValidationException
{
    /** @var int */
    protected $minItems;

    /**
     * MinItemsException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $minItems
     */
    public function __construct($providedValue, string $propertyName, int $minItems)
    {
        $this->minItems = $minItems;

        parent::__construct(
            "Array $propertyName must not contain less than $minItems items",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMinItems(): int
    {
        return $this->minItems;
    }
}
