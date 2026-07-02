<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class EnumException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class EnumException extends ValidationException
{
    /**
     * EnumException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $allowedValues
    ) {
        parent::__construct(
            "Invalid value for $propertyName declined by enum constraint",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }
}
