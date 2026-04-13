<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

class MaxContainsException extends ValidationException
{
    public function __construct(
        array $providedValue,
        string $propertyName,
        protected int $maxContains,
        protected int $matches,
    ) {
        parent::__construct(
            sprintf(
                'Array %s must not contain more than %d items matching the contains constraint, %s matching items provided',
                $propertyName,
                $this->maxContains,
                $this->matches,
            ),
            $propertyName,
            $providedValue
        );
    }

    public function getMaxContains(): int
    {
        return $this->maxContains;
    }

    public function getMatches(): int
    {
        return $this->matches;
    }
}
