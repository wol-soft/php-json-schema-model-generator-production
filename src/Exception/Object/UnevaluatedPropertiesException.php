<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class UnevaluatedPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class UnevaluatedPropertiesException extends ValidationException
{
    /** @var string[] */
    protected $unevaluatedProperties;

    /**
     * UnevaluatedPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param string[] $unevaluatedProperties
     */
    public function __construct($providedValue, string $propertyName, array $unevaluatedProperties)
    {
        $this->unevaluatedProperties = $unevaluatedProperties;

        parent::__construct(
            "Provided JSON for $propertyName contains not allowed unevaluated properties [" .
                join(", ", $unevaluatedProperties) . ']',
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return string[]
     */
    public function getUnevaluatedProperties(): array
    {
        return $this->unevaluatedProperties;
    }
}
