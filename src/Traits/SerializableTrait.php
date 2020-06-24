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
     * @param array $except provide a list of properties which shouldn't be contained in the resulting JSON.
     *                      eg. if you want to return an user model and don't want the password to be included
     * @param int $options  Bitmask for json_encode
     * @param int $depth    the maximum level of object nesting. Must be greater than 0
     *
     * @return string|false
     */
    public function toJSON(array $except = [], int $options = 0, int $depth = 512)
    {
        if ($depth < 1) {
            return false;
        }

        return json_encode($this->toArray($except, $depth), $options, $depth);
    }

    /**
     * Get an array representation of the current state
     *
     * @param array $except provide a list of properties which shouldn't be contained in the resulting JSON.
     *                      eg. if you want to return an user model and don't want the password to be included
     * @param int $depth    the maximum level of object nesting. Must be greater than 0
     *
     * @return array|false
     */
    public function toArray(array $except = [], int $depth = 512)
    {
        if ($depth < 1) {
            return false;
        }

        $depth--;
        $modelData = [];
        array_push($except, 'rawModelDataInput', 'errorRegistry');

        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, $except)) {
                continue;
            }

            if (is_array($value)) {
                $subData = [];
                foreach ($value as $subKey => $element) {
                    $subData[$subKey] = $this->evaluateAttribute($key, $element, $depth, $except);
                }
                $modelData[$key] = $subData;
            } else {
                $modelData[$key] = $this->evaluateAttribute($key, $value, $depth, $except);
            }
        }

        return $modelData;
    }

    private function evaluateAttribute(string $property, $attribute, int $depth, array $except)
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
                ? $attribute->toArray($except, $depth - 1)
                : get_object_vars($attribute)
            );
    }

    /**
     * Return a JSON serializable representation of the current state
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
