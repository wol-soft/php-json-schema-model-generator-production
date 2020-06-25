<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class ExclusiveMaximumException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class ExclusiveMaximumException extends ValidationException
{
    /** @var int|float */
    protected $exclusiveMaximum;

    /**
     * ExclusiveMaximumException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int|float $exclusiveMaximum
     */
    public function __construct($providedValue, string $propertyName, $exclusiveMaximum)
    {
        $this->exclusiveMaximum = $exclusiveMaximum;

        parent::__construct(
            "Value for $propertyName must be smaller than $exclusiveMaximum",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int|float
     */
    public function getExclusiveMaximum()
    {
        return $this->exclusiveMaximum;
    }
}
