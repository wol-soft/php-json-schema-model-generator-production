<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidComposedValueException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
abstract class InvalidComposedValueException extends ValidationException
{
    protected const COMPOSED_ERROR_MESSAGE = '';

    /** @var int */
    protected $succeededCompositionElements;
    /** @var ValidationException[][] */
    protected $compositionErrorCollection;
    /**
     * InvalidComposedValueException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param int $succeededCompositionElements
     * @param ValidationException[][] $compositionErrorCollection
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        int $succeededCompositionElements,
        array $compositionErrorCollection
    ) {
        $this->succeededCompositionElements = $succeededCompositionElements;
        $this->compositionErrorCollection = $compositionErrorCollection;

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

    /**
     * @return int
     */
    public function getSucceededCompositionElements(): int
    {
        return $this->succeededCompositionElements;
    }

    /**
     * @return ValidationException[][]
     */
    public function getCompositionErrorCollection(): array
    {
        return $this->compositionErrorCollection;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $compositionIndex = 0;

        return "Invalid value for $propertyName declined by composition constraint.\n  " .
            sprintf(static::COMPOSED_ERROR_MESSAGE, $this->succeededCompositionElements) .
            array_reduce(
                $this->compositionErrorCollection,
                function (string $carry, ErrorRegistryExceptionInterface $exception) use (&$compositionIndex): string {
                    return "$carry\n  - Composition element #" . ++$compositionIndex . (
                        $exception->getErrors()
                            ? ": Failed\n    * " .
                                implode(
                                    "\n    * ",
                                    array_map(function (ValidationException $exception): string {
                                        return $exception->getMessage();
                                    }, $exception->getErrors())
                                )
                            : ': Valid'
                        );
                },
                ''
            );
    }
}
