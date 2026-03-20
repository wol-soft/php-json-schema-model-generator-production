<?php

declare(strict_types = 1);

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
    public function __construct($providedValue, string $propertyName, protected array $missingAttributes)
    {
        parent::__construct(
            "Missing required attributes which are dependants of $propertyName:\n  - " .
                join("\n  - ", $this->missingAttributes),
            $propertyName,
            $providedValue
        );
    }

    public function getMissingAttributes(): array
    {
        return $this->missingAttributes;
    }
}
