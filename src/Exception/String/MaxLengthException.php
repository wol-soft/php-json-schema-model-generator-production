<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaxLengthException
 *
 * @package PHPModelGenerator\Exception\String
 */
class MaxLengthException extends ValidationException
{
    /**
     * MaxLengthException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $maximumLength)
    {
        parent::__construct(
            "Value for $propertyName must not be longer than {$this->maximumLength}",
            $propertyName,
            $providedValue
        );
    }

    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
