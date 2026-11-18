<?php

declare(strict_types=1);

namespace PHPModelGenerator\Utils;

use PHPModelGenerator\Interfaces\JSONModelInterface;

/**
 * Comparison of values decoded from JSON, as done by the `enum` and `const` keywords. The generator uses the very same
 * comparison while it generates the code (removing duplicated allowed values, checking a default against an enum) and
 * the generated code calls it to validate the provided data, so both sides always follow the same rules.
 *
 * Structures are equal if they hold the same keys with equal values, independent of the order of the keys of an
 * object (the order of a list matters). A number inside of a structure equals a number of the other type with the same
 * value (1 and 1.0), 0, false, null, "" and "0" are all different from each other. A scalar which is not part of a
 * structure is compared with its type: the type of the property which holds the value is derived from the allowed
 * values, an integer must not be accepted for a float and vice versa.
 *
 * A loose comparison (==) was rejected: it equals 0, false, null, "" and "0". Sorting the keys of both values and
 * comparing the results strictly was rejected as well: numbers of different types inside of a structure (1 and 1.0)
 * must be equal, which a strict comparison of arrays rejects.
 */
final class JsonValue
{
    public static function equals(mixed $left, mixed $right): bool
    {
        return self::compare($left, $right, false);
    }

    public static function contains(array $values, mixed $needle): bool
    {
        foreach ($values as $value) {
            if (self::equals($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the values which equal a value which is already part of the list. Unlike array_unique() the values are
     * not compared as strings, which would merge 1, "1" and true and can't handle arrays.
     *
     * @return mixed[] A list of the remaining values
     */
    public static function unique(array $values): array
    {
        $uniqueValues = [];

        foreach ($values as $value) {
            if (!self::contains($uniqueValues, $value)) {
                $uniqueValues[] = $value;
            }
        }

        return $uniqueValues;
    }

    /**
     * The data of a value of a model: a model (the value of an object property, also inside of an array) is replaced
     * by the data it was set up with. A comparison with the model itself, or with an array cast of it, would never
     * match the data of the schema.
     */
    public static function normalize(mixed $value): mixed
    {
        if ($value instanceof JSONModelInterface) {
            $value = $value->meta()->rawInput();
        }

        return is_array($value) ? array_map(self::normalize(...), $value) : $value;
    }

    private static function compare(mixed $left, mixed $right, bool $nested): bool
    {
        if (is_array($left) && is_array($right)) {
            if (count($left) !== count($right)) {
                return false;
            }

            foreach ($left as $key => $item) {
                if (!array_key_exists($key, $right) || !self::compare($item, $right[$key], true)) {
                    return false;
                }
            }

            return true;
        }

        if ($nested && (is_int($left) || is_float($left)) && (is_int($right) || is_float($right))) {
            return $left == $right;
        }

        return $left === $right;
    }
}
