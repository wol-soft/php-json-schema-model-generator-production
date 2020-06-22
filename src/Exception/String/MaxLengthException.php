<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MaxLengthException
 *
 * @package PHPModelGenerator\Exception\String
 */
class MaxLengthException extends ValidationException
{
    /** @var int */
    protected $maximumLength;

    /**
     * MaxLengthException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $maximumLength
     */
    public function __construct($providedValue, string $propertyName, int $maximumLength)
    {
        $this->maximumLength = $maximumLength;

        parent::__construct(
            "Value for $propertyName must not be longer than $maximumLength",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
