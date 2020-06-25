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
    /** @var int */
    protected $minimumLength;

    /**
     * MinLengthException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $minimumLength
     */
    public function __construct($providedValue, string $propertyName, int $minimumLength)
    {
        $this->minimumLength = $minimumLength;

        parent::__construct(
            "Value for $propertyName must not be shorter than $minimumLength",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMinimumLength(): int
    {
        return $this->minimumLength;
    }
}
