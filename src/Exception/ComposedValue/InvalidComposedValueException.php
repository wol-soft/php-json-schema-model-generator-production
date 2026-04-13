<?php

declare(strict_types=1);

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
    /**
     * InvalidComposedValueException constructor.
     *
     * @param $providedValue
     * @param ValidationException[][] $compositionErrorCollection
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        protected int $succeededCompositionElements,
        protected array $compositionErrorCollection
    ) {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue);
    }

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
                                    array_map(fn(ValidationException $exception): string => $exception->getMessage(), $exception->getErrors())
                                )
                            : ': Valid'
                        );
                },
                ''
            );
    }
}
