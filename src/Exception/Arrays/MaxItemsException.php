<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaxItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class MaxItemsException extends ValidationException
{
    /**
     * MaxItemsException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $maxItems)
    {
        parent::__construct(
            "Array $propertyName must not contain more than {$this->maxItems} items",
            $propertyName,
            $providedValue
        );
    }

    public function getMaxItems(): int
    {
        return $this->maxItems;
    }
}
