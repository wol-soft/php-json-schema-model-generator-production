<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Dependency;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidPropertyDependencyException
 *
 * @package PHPModelGenerator\Exception\Dependency
 */
class InvalidPropertyDependencyException extends ValidationException
{
    /**
     * InvalidPropertyDependencyException constructor.
     *
     * @param        $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $missingAttributes
    ) {
        parent::__construct(
            "Missing required attributes which are dependants of $propertyName:\n  - " .
                join("\n  - ", $this->missingAttributes),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getMissingAttributes(): array
    {
        return $this->missingAttributes;
    }
}
