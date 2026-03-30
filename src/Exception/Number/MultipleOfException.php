<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MultipleOfException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class MultipleOfException extends ValidationException
{
    /**
     * MultipleOfException constructor.
     *
     * @param $providedValue
     * @param int|float $multipleOf
     */
    public function __construct($providedValue, string $propertyName, protected $multipleOf)
    {
        parent::__construct(
            "Value for $propertyName must be a multiple of {$this->multipleOf}",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int|float
     */
    public function getMultipleOf()
    {
        return $this->multipleOf;
    }
}
