<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaximumException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class MaximumException extends ValidationException
{
    /** @var int|float */
    protected $maximum;

    /**
     * MaximumException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int|float $maximum
     */
    public function __construct($providedValue, string $propertyName, $maximum)
    {
        $this->maximum = $maximum;

        parent::__construct(
            "Value for $propertyName must not be larger than $maximum",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int|float
     */
    public function getMaximum()
    {
        return $this->maximum;
    }
}
