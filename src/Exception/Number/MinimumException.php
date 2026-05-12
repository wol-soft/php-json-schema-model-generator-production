<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Number;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinimumException
 *
 * @package PHPModelGenerator\Exception\Number
 */
class MinimumException extends ValidationException
{
    /**
     * MinimumException constructor.
     *
     * @param $providedValue
     * @param int|float $minimum
     */
    public function __construct($providedValue, string $propertyName, protected $minimum)
    {
        parent::__construct(
            "Value for $propertyName must not be smaller than {$this->minimum}",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int|float
     */
    public function getMinimum()
    {
        return $this->minimum;
    }
}
