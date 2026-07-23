<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Object;

use Exception;
use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class NotAllowedAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class NestedObjectException extends ValidationException
{
    /**
     * NotAllowedAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        private readonly Exception $nestedException
    ) {
        if ($this->nestedException instanceof ErrorRegistryExceptionInterface) {
            foreach ($this->nestedException->getErrors() as $error) {
                $error->setInstancePointerParent($this);
            }
        } elseif ($this->nestedException instanceof ValidationException) {
            $this->nestedException->setInstancePointerParent($this);
        }

        parent::__construct(
            "Invalid nested object for property '$propertyName':\n  - " .
                MessageFormatter::flattenNestedMessage($this->nestedException->getMessage()),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getNestedException(): Exception
    {
        return $this->nestedException;
    }
}
