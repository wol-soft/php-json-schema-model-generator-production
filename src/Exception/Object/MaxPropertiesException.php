<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

class MaxPropertiesException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected int $maxProperties,
        protected int $count,
    ) {
        parent::__construct(
            "Provided object for '$propertyName' must not contain more than {$this->maxProperties} properties",
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getMaxProperties(): int
    {
        return $this->maxProperties;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
