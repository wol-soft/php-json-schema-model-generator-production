<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\ComposedValue;

use PHPModelGenerator\Exception\ErrorRegistryExceptionInterface;
use PHPModelGenerator\Exception\ValidationException;

abstract class InvalidComposedValueException extends ValidationException
{
    protected const COMPOSED_ERROR_MESSAGE = '';
    protected const COMPOSED_KEYWORD = '';

    protected int $succeededCompositionElements;
    protected array $compositionErrorCollection;
    protected array $branchDescriptions;
    protected array $discriminatorInfo;

    public function __construct(
        $providedValue,
        string $propertyName,
        int $succeededCompositionElements,
        array $compositionErrorCollection,
        array $branchDescriptions = [],
        array $discriminatorInfo = [],
    ) {
        $this->succeededCompositionElements = $succeededCompositionElements;
        $this->compositionErrorCollection = $compositionErrorCollection;
        $this->branchDescriptions = $branchDescriptions;
        $this->discriminatorInfo = $discriminatorInfo;

        parent::__construct($this->buildMessage($propertyName, $providedValue), $propertyName, $providedValue);
    }

    public function getSucceededCompositionElements(): int
    {
        return $this->succeededCompositionElements;
    }

    public function getCompositionErrorCollection(): array
    {
        return $this->compositionErrorCollection;
    }

    public function getBranchDescriptions(): array
    {
        return $this->branchDescriptions;
    }

    public function getDiscriminatorInfo(): array
    {
        return $this->discriminatorInfo;
    }

    private function buildMessage(string $propertyName, $providedValue): string
    {
        $compositionIndex = 0;

        return "Invalid value for $propertyName declined by composition constraint.\n  " .
            sprintf(static::COMPOSED_ERROR_MESSAGE, $this->succeededCompositionElements) .
            array_reduce(
                $this->compositionErrorCollection,
                function (string $carry, ErrorRegistryExceptionInterface $exception) use (&$compositionIndex): string {
                    $index = ++$compositionIndex;
                    $description = $this->branchDescriptions[$index - 1] ?? '';

                    $line = "  - Composition element #$index";
                    if ($description !== '') {
                        $line .= " ($description)";
                    }

                    return "$carry\n$line" . (
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

    private function formatProvidedValue($value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            $truncated = mb_strlen($value) > 80 ? mb_substr($value, 0, 77) . '...' : $value;
            return "'$truncated'";
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return $this->formatArrayValue($value);
        }

        if (is_object($value)) {
            $className = get_class($value);
            $shortName = substr($className, strrpos($className, '\\') !== false ? strrpos($className, '\\') + 1 : 0);
            return "object($shortName)";
        }

        return (string) $value;
    }

    private function formatArrayValue(array $value): string
    {
        $keys = array_keys($value);
        $keyList = array_map(
            static fn ($k): string => is_string($k) ? "'$k'" : (string) $k,
            $keys,
        );
        $list = implode(', ', array_slice($keyList, 0, 10));
        if (count($keys) > 10) {
            $list .= ', ...';
        }
        return 'array keys: [' . $list . ']';
    }
}
