<?php

declare(strict_types=1);

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
    public function __construct($providedValue, string $propertyName, string $jsonPointer, protected int $minimumLength)
    {
        parent::__construct(
            "Value for '$propertyName' must not be shorter than {$this->minimumLength}",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getMinimumLength(): int
    {
        return $this->minimumLength;
    }
}
