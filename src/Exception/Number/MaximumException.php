<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaximumException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class MaximumException extends ValidationException
{
    /**
     * MaximumException constructor.
     *
     * @param $providedValue
     * @param int|float $maximum
     */
    public function __construct($providedValue, string $propertyName, protected $maximum)
    {
        parent::__construct(
            "Value for $propertyName must not be larger than {$this->maximum}",
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
