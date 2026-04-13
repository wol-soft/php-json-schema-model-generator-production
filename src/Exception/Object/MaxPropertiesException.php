<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

class MaxPropertiesException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        protected int $maxProperties,
        protected int $count,
    ) {
        parent::__construct(
            sprintf(
                'Provided object for %s must not contain more than %d properties, %d properties provided',
                $propertyName,
                $this->maxProperties,
                $this->count,
            ),
            $propertyName,
            $providedValue
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
