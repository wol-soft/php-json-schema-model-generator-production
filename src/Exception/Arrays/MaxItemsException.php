<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaxItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class MaxItemsException extends ValidationException
{
    /** @var int */
    protected $maxItems;

    /**
     * MaxItemsException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $maxItems
     */
    public function __construct($providedValue, string $propertyName, int $maxItems)
    {
        $this->maxItems = $maxItems;

        parent::__construct(
            "Array $propertyName must not contain more than $maxItems items",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMaxItems(): int
    {
        return $this->maxItems;
    }
}
