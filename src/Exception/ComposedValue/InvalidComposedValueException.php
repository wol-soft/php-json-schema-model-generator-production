<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\ComposedValue;

use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

abstract class InvalidComposedValueException extends ValidationException
{
    protected const COMPOSED_ERROR_MESSAGE = '';

    /**
     * @param $providedValue
     * @param ErrorRegistryExceptionInterface[] $compositionErrorCollection The errors of each composition branch
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected int $succeededCompositionElements,
        protected array $compositionErrorCollection
    ) {
        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);

        // A composition branch validates the same value at the same position as the composition
        // itself: branch errors raised for the composed property consume no path segment of their
        // own and inherit wherever this composition ends up, see linkBranchExceptions().
        foreach ($this->compositionErrorCollection as $branch) {
            $this->linkBranchExceptions($branch);
        }
    }

    public function getSucceededCompositionElements(): int
    {
        return $this->succeededCompositionElements;
    }

    /**
     * @return ErrorRegistryExceptionInterface[]
     */
    public function getCompositionErrorCollection(): array
    {
        return $this->compositionErrorCollection;
    }

    protected function getErrorMessage(string $propertyName): string
    {
        $compositionIndex = 0;

        return "Invalid value for '$propertyName' declined by composition constraint\n  " .
            sprintf(
                static::COMPOSED_ERROR_MESSAGE,
                MessageFormatter::pluralize($this->succeededCompositionElements, 'element'),
            ) .
            array_reduce(
                $this->compositionErrorCollection,
                function (string $carry, ErrorRegistryExceptionInterface $exception) use (&$compositionIndex): string {
                    return "$carry\n  - Composition element #" . ++$compositionIndex . (
                        $exception->getErrors()
                            ? ": Failed\n    * " . MessageFormatter::bulletList($exception->getErrors())
                            : ': Valid'
                        );
                },
                ''
            );
    }
}
