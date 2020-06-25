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
    /** @var int|float */
    protected $multipleOf;

    /**
     * MultipleOfException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int|float $multipleOf
     */
    public function __construct($providedValue, string $propertyName, $multipleOf)
    {
        $this->multipleOf = $multipleOf;

        parent::__construct(
            "Value for $propertyName must be a multiple of $multipleOf",
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
