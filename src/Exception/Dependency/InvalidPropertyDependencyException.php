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
    /** @var array */
    protected $missingAttributes;

    /**
     * InvalidPropertyDependencyException constructor.
     *
     * @param        $providedValue
     * @param string $propertyName
     * @param array  $missingAttributes
     */
    public function __construct($providedValue, string $propertyName, array $missingAttributes)
    {
        $this->missingAttributes = $missingAttributes;

        parent::__construct(
            "Missing required attributes which are dependants of $propertyName:\n  - " .
                join("\n  - ", $missingAttributes),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return array
     */
    public function getMissingAttributes(): array
    {
        return $this->missingAttributes;
    }
}
