<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

use Exception;
use PHPModelGenerator\Exception\ValidationException;

/**
 * Class DateTime
 *
 * @package PHPModelGenerator\Filter
 */
class DateTime
{
    /**
     * @param string|null $value
     * @param array $options
     *
     * @return \DateTime|null
     *
     * @throws ValidationException
     */
    public static function filter(?string $value, array $options = []): ?\DateTime
    {
        try {
            if ($options['convertNullToNow'] ?? false && $value === null) {
                return new \DateTime();
            }

            if ($options['denyEmptyValue'] ?? false && $value === '') {
                throw new ValidationException("Can't process an empty date value");
            }

            return $value !== null ? new \DateTime($value) : null;
        } catch (Exception $e) {
            throw new ValidationException('Invalid Date Time value', $e);
        }
    }
}
