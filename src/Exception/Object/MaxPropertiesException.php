<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinLengthException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class MaxPropertiesException extends ValidationException
{
    /** @var int */
    protected $maxProperties;

    /**
     * MaxPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $maxProperties
     */
    public function __construct($providedValue, string $propertyName, int $maxProperties)
    {
        $this->maxProperties = $maxProperties;

        parent::__construct(
            "Provided object for $propertyName must not contain more than $maxProperties properties",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getMaxProperties(): int
    {
        return $this->maxProperties;
    }
}
