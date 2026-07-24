<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class UnevaluatedPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class UnevaluatedPropertiesException extends ValidationException
{
    /**
     * @param $providedValue
     * @param string[] $unevaluatedProperties
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $unevaluatedProperties,
    ) {
        parent::__construct(
            "Provided JSON for '$propertyName' contains not allowed unevaluated properties [" .
                MessageFormatter::quotedList($this->unevaluatedProperties) . ']',
            $propertyName,
            $providedValue,
            $jsonPointer,
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
