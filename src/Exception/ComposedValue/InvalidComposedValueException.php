<?php

declare(strict_types = 1);

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

        parent::__construct($this->buildMessage($propertyName), $propertyName, $providedValue);
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

    private function buildMessage(string $propertyName): string
    {
        $totalBranches = $this->branchDescriptions !== []
            ? count($this->branchDescriptions)
            : count($this->compositionErrorCollection);
        $succeeded = $this->succeededCompositionElements;
        $keyword = static::COMPOSED_KEYWORD ?: 'composition';
        $header = $this->buildHeader($propertyName, $succeeded, $totalBranches, $keyword);

        $hasDetailedErrors = $this->compositionErrorCollection !== [];

        if ($hasDetailedErrors) {
            $matchedLines = [];
            $failedLines = [];

            $branchCount = count($this->compositionErrorCollection);

            for ($i = 0; $i < $branchCount; $i++) {
                $branchNum = $i + 1;
                $description = $this->branchDescriptions[$i] ?? '';

                $line = "  #$branchNum";
                if ($description !== '') {
                    $line .= " ($description)";
                }

                $errors = $this->compositionErrorCollection[$i]->getErrors();
                if ($errors !== []) {
                    $errorTexts = array_map(
                        static fn (ValidationException $e): string => $e->getMessage(),
                        $errors,
                    );
                    $line .= ": Failed\n    * " . implode("\n    * ", $errorTexts);
                    $failedLines[] = $line;
                } else {
                    $line .= ': Valid';
                    $matchedLines[] = $line;
                }
            }

            $sections = [];

            if ($this->isAllFailedCase($succeeded, $totalBranches)) {
                $sections[] = "  [ALL BRANCHES FAILED]\n" . implode("\n", $failedLines);
            } else {
                if ($matchedLines !== []) {
                    $matchLabel = $this->getMatchLabel($succeeded, $totalBranches);
                    $sections[] = "  $matchLabel\n" . implode("\n", $matchedLines);
                }

                if ($failedLines !== []) {
                    $sections[] = "  [FAILED]\n" . implode("\n", $failedLines);
                }
            }

            if ($sections !== []) {
                $header .= "\n" . implode("\n\n", $sections);
            }
        }

        $header .= "\n\n  Provided value: " . $this->formatProvidedValue($this->providedValue);

        return $header;
    }

    private function buildHeader(string $propertyName, int $succeeded, int $total, string $keyword): string
    {
        $base = "Invalid value for '$propertyName'. Must match ";

        if (static::COMPOSED_ERROR_MESSAGE === 'Requires to match all composition elements but matched %s elements.') {
            return $base . "all $total ${keyword} branches, but only $succeeded matched.";
        }

        if (static::COMPOSED_ERROR_MESSAGE === 'Requires to match at least one composition element.') {
            return $base . "at least 1 of $total ${keyword} branches, but 0 matched.";
        }

        if (static::COMPOSED_ERROR_MESSAGE === 'Requires to match one composition element but matched %s elements.') {
            $discriminatorHint = '';
            if (
                $this->discriminatorInfo !== []
                && isset($this->discriminatorInfo['propertyName'])
                && isset($this->discriminatorInfo['mapping'])
            ) {
                $discriminatorHint = $this->buildDiscriminatorHint();
            }

            if ($succeeded === 0) {
                $msg = "exactly 1 of $total ${keyword} branches, but 0 matched.";
                return $discriminatorHint !== ''
                    ? "$base$msg\n\n  $discriminatorHint"
                    : "$base$msg";
            }

            $msg = "exactly 1 of $total ${keyword} branches, but $succeeded matched.";
            return $discriminatorHint !== ''
                ? "$base$msg\n\n  $discriminatorHint"
                : "$base$msg";
        }

        if (static::COMPOSED_ERROR_MESSAGE === 'Requires to match none composition element but matched %s elements.') {
            return $base . "none of the $total ${keyword} branches, but $succeeded matched.";
        }

        return $base . sprintf(static::COMPOSED_ERROR_MESSAGE, $succeeded);
    }

    private function isAllFailedCase(int $succeeded, int $total): bool
    {
        return $succeeded === 0 && $total > 0;
    }

    private function getMatchLabel(int $succeeded, int $total): string
    {
        $isOneOf = static::COMPOSED_ERROR_MESSAGE === 'Requires to match one composition element but matched %s elements.';

        if ($isOneOf && $succeeded > 1) {
            return "[MATCHED - $succeeded branches match, only 1 should]";
        }

        return '[MATCHED]';
    }

    private function buildDiscriminatorHint(): string
    {
        $propName = $this->discriminatorInfo['propertyName'];
        $mapping = $this->discriminatorInfo['mapping'];
        $validValues = array_keys($mapping);
        $validValuesStr = implode("', '", $validValues);

        return "  Discriminator: property '$propName' determines which branch applies.\n  Expected '$propName' to be one of: '$validValuesStr'.";
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
