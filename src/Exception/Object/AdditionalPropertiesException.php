<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class NotAllowedAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class AdditionalPropertiesException extends ValidationException
{
    /** @var string[] */
    protected $additionalProperties;

    /**
     * NotAllowedAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string[] $additionalProperties
     */
    public function __construct($providedValue, string $propertyName, array $additionalProperties)
    {
        $this->additionalProperties= $additionalProperties;

        parent::__construct(
            "Provided JSON for $propertyName contains not allowed additional properties [" .
                join(", ", $additionalProperties) . ']',
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
