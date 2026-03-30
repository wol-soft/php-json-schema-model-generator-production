<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinLengthException
 *
 * @package PHPModelGenerator\Exception\String
 */
class MinLengthException extends ValidationException
{
    /**
     * MinLengthException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $minimumLength)
    {
        parent::__construct(
            "Value for $propertyName must not be shorter than {$this->minimumLength}",
            $propertyName,
            $providedValue
        );
    }

    public function getMinimumLength(): int
    {
        return $this->minimumLength;
    }
}
