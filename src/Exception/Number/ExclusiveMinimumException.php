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
    /**
     * ExclusiveMinimumException constructor.
     *
     * @param $providedValue
     * @param int|float $exclusiveMinimum
     */
    public function __construct($providedValue, string $propertyName, protected $exclusiveMinimum)
    {
        parent::__construct(
            "Value for $propertyName must be larger than {$this->exclusiveMinimum}",
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
