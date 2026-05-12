<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class NotAllowedAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class AdditionalPropertiesException extends ValidationException
{
    /**
     * NotAllowedAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param string[] $additionalProperties
     */
    public function __construct($providedValue, string $propertyName, protected array $additionalProperties)
    {
        parent::__construct(
            "Provided JSON for $propertyName contains not allowed additional properties [" .
                join(", ", $this->additionalProperties) . ']',
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string[]
     */
    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }
}
