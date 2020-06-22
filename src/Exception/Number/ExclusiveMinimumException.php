<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class ExclusiveMinimumException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class ExclusiveMinimumException extends ValidationException
{
    /** @var int|float */
    protected $exclusiveMinimum;

    /**
     * ExclusiveMinimumException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int|float $exclusiveMinimum
     */
    public function __construct($providedValue, string $propertyName, $exclusiveMinimum)
    {
        $this->exclusiveMinimum = $exclusiveMinimum;

        parent::__construct(
            "Value for $propertyName must be larger than $exclusiveMinimum",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int|float
     */
    public function getExclusiveMinimum()
    {
        return $this->exclusiveMinimum;
    }
}
