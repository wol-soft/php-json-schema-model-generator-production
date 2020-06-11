<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Traits;

/**
 * Provide methods to serialize generated models
 *
 * Trait SerializableTrait
 *
 * @package PHPModelGenerator\Traits
 */
trait SerializableTrait
{
    /**
     * Get a JSON representation of the current state
     *
     * @param int $options Bitmask for json_encode
     * @param int $depth   the maximum level of object nesting. Must be greater than 0
     *
     * @return string|false
     */
    public function toJSON(int $options = 0, int $depth = 512)
    {
        if ($depth < 1) {
            return false;
        }

        return json_encode($this->toArray($depth), $options, $depth);
    }

    /**
     * Get an array representation of the current state
     *
     * @param int $depth the maximum level of object nesting. Must be greater than 0
     *
     * @return array|false
     */
    public function toArray(int $depth = 512)
    {
        if ($depth < 1) {
            return false;
        }

        $depth--;
        $modelData = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, ['rawModelDataInput', 'errorRegistry'])) {
                continue;
            }

            if (is_array($value)) {
                $subData = [];
                foreach ($value as $subKey => $element) {
                    $subData[$subKey] = $this->evaluateAttribute($key, $element, $depth);
                }
                $modelData[$key] = $subData;
            } else {
                $modelData[$key] = $this->evaluateAttribute($key, $value, $depth);
            }
        }

        return $modelData;
    }

    private function evaluateAttribute(string $property, $attribute, int $depth)
    {
        $customSerializer = 'serialize' . ucfirst($property);
        if (method_exists($this, $customSerializer)) {
            return $this->{$customSerializer}();
        }

        if (!is_object($attribute)) {
            return $attribute;
        }

        if ($depth === 0 && method_exists($attribute, '__toString')) {
            return (string) $attribute;
        }

        return (0 >= $depth)
            ? null
            : (
            method_exists($attribute, 'toArray')
                ? $attribute->toArray($depth - 1)
                : get_object_vars($attribute)
            );
    }
}
