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
        string $jsonPointer,
        protected int $succeededCompositionElements,
        protected array $compositionErrorCollection
    ) {
        // A composition branch validates the same value at the same position as the composition
        // itself — it consumes no path segment of its own, so branch errors only need the parent
        // link (not a segment replacement) to inherit wherever this composition ends up.
        foreach ($this->compositionErrorCollection as $branch) {
            foreach ($branch->getErrors() as $error) {
                $error->setInstancePointerParent($this);
            }
        }

        parent::__construct($this->getErrorMessage($propertyName), $propertyName, $providedValue, $jsonPointer);
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
                                    str_replace(
                                        "\n",
                                        "\n    ",
                                        array_map(
                                            fn(ValidationException $exception): string => $exception->getMessage(),
                                            $exception->getErrors(),
                                        )
                                    )
                                )
                            : ': Valid'
                        );
                },
                ''
            );
    }
}
