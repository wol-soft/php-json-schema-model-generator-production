<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\MessageFormatter;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class EnumException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class EnumException extends ValidationException
{
    /** Beyond this many allowed values, the message shows a truncated preview instead of the full list. */
    private const MAX_DISPLAYED_VALUES = 8;
    /** How many values to show in the truncated preview. */
    private const TRUNCATED_DISPLAY_COUNT = 5;

    /**
     * EnumException constructor.
     *
     * @param $providedValue
     */
    public function __construct(
        $providedValue,
        string $propertyName,
        string $jsonPointer,
        protected array $allowedValues
    ) {
        parent::__construct(
            sprintf(
                "Value for '%s' must be one of [%s], got %s",
                $propertyName,
                self::formatAllowedValues($this->allowedValues),
                MessageFormatter::format($providedValue),
            ),
            $propertyName,
            $providedValue,
            $jsonPointer
        );
    }

    public function getAllowedValues(): array
    {
        return $this->allowedValues;
    }

    private static function formatAllowedValues(array $allowedValues): string
    {
        if (count($allowedValues) <= self::MAX_DISPLAYED_VALUES) {
            return implode(',', array_map(MessageFormatter::format(...), $allowedValues));
        }

        $shown = array_slice($allowedValues, 0, self::TRUNCATED_DISPLAY_COUNT);
        $remaining = count($allowedValues) - self::TRUNCATED_DISPLAY_COUNT;

        return implode(',', array_map(MessageFormatter::format(...), $shown))
            . ", ... (and $remaining more)";
    }
}
