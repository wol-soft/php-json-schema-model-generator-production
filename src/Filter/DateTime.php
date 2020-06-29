<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

use Exception;

/**
 * Class DateTime
 *
 * @package PHPModelGenerator\Filter
 */
class DateTime
{
    /**
     * @param string|null $value
     * @param array       $options
     *
     * @return \DateTime|null
     *
     * @throws Exception
     */
    public static function filter(?string $value, array $options = []): ?\DateTime
    {
        static::convertConstants($options);

        try {
            if (($options['convertNullToNow'] ?? false) && $value === null) {
                return new \DateTime();
            }

            if (($options['denyEmptyValue'] ?? false) && $value === '') {
                throw new Exception("Can't process an empty date value");
            }

            if (($options['convertEmptyValueToNull'] ?? false) && $value === '') {
                return null;
            }

            if (($options['createFromFormat'] ?? false) && $value !== null) {
                $result = \DateTime::createFromFormat($options['createFromFormat'], $value);

                if (!$result) {
                    throw new Exception();
                }

                return $result;
            }

            return $value !== null ? new \DateTime($value) : null;
        } catch (Exception $e) {
            throw new Exception("Invalid Date Time value \"$value\"", 0, $e);
        }
    }

    /**
     * @param \DateTime|null $value
     * @param array $options
     *
     * @return string|null
     */
    public static function serialize(?\DateTime $value, array $options = []): ?string
    {
        static::convertConstants($options);

        return ($value instanceof \DateTime)
            ? $value->format($options['outputFormat'] ?? $options['createFromFormat'] ?? DATE_ISO8601)
            : null;
    }

    /**
     * Make DATE constants available
     *
     * @param array $options
     */
    protected static function convertConstants(array &$options): void
    {
        static $constants = [];

        foreach (['createFromFormat', 'outputFormat'] as $format) {
            if (!isset($options[$format])) {
                continue;
            }

            if (isset($constants[$options[$format]])) {
                $options[$format] = $constants[$options[$format]];
                continue;
            }

            if (defined("DATE_{$options[$format]}")) {
                $options[$format] = $constants[$options[$format]] = constant("DATE_{$options[$format]}");
            }
        }
    }
}
