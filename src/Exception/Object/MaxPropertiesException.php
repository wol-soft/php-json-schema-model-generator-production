<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class MinLengthException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class MaxPropertiesException extends ValidationException
{
    /**
     * MaxPropertiesException constructor.
     *
     * @param $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $maxProperties)
    {
        parent::__construct(
            "Provided object for $propertyName must not contain more than {$this->maxProperties} properties",
            $propertyName,
            $providedValue
        );
    }

    public function getMaxProperties(): int
    {
        return $this->maxProperties;
    }
}
