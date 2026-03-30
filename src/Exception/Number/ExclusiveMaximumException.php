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
    /**
     * ExclusiveMaximumException constructor.
     *
     * @param $providedValue
     * @param int|float $exclusiveMaximum
     */
    public function __construct($providedValue, string $propertyName, protected $exclusiveMaximum)
    {
        parent::__construct(
            "Value for $propertyName must be smaller than {$this->exclusiveMaximum}",
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
