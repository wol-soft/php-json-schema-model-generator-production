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
    private static $_customSerializer = [];

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
     * Return a JSON serializable representation of the current state
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
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

        if (isset($this->_skipNotProvidedPropertiesMap)) {
            $except = array_merge(
                $except,
                array_diff($this->_skipNotProvidedPropertiesMap, array_keys($this->_rawModelDataInput))
            );
        }

        foreach (get_class_vars(get_class($this)) as $key => $value) {
            if (in_array($key, $except) || strstr($key, '_') !== false) {
                continue;
            }

            if ($customSerializer = $this->_getCustomSerializerMethod($key)) {
                $modelData[$key] = $this->_getSerializedValue($this->{$customSerializer}(), $depth, $except);
                continue;
            }

            $modelData[$key] = $this->_getSerializedValue($this->$key, $depth, $except);
        }

        return $this->resolveSerializationHook($modelData, $depth, $except);
    }

    /**
     * Function can be overwritten by classes using the trait to hook into serialization
     */
    protected function resolveSerializationHook(array $data, int $depth, array $except): array
    {
        return $data;
    }

    private function _getSerializedValue($value, int $depth, array $except) {
        if (is_array($value)) {
            return array_map(
                fn (mixed $element): mixed => $this->evaluateAttribute($element, $depth - 1, $except),
                $value,
            );
        }

        return $this->evaluateAttribute($value, $depth, $except);
    }

    private function evaluateAttribute($attribute, int $depth, array $except)
    {
        if ($depth < 0) {
            return null;
        }

        if (is_array($attribute)) {
            return array_map(
                fn (mixed $element): mixed => $this->evaluateAttribute($element, $depth - 1, $except),
                $attribute,
            );
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

    private function _getCustomSerializerMethod(string $property) {
        if (isset(self::$_customSerializer[$property])) {
            return self::$_customSerializer[$property];
        }

        $customSerializer = 'serialize' . ucfirst($property);
        if (!method_exists($this, $customSerializer)) {
            $customSerializer = false;
        }

        return self::$_customSerializer[$property] = $customSerializer;
    }
}
