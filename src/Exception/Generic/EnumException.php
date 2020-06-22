<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class EnumException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class EnumException extends ValidationException
{
    /** @var array */
    protected $allowedValues;

    /**
     * EnumException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param array $allowedValues
     */
    public function __construct($providedValue, string $propertyName, array $allowedValues)
    {
        $this->allowedValues = $allowedValues;

        parent::__construct(
            "Invalid value for $propertyName declined by enum constraint",
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return array
     */
    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }
}
