<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Traits;

use stdClass;

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

        return json_encode($this->_getValues($depth, $except, true), $options, $depth);
    }

    /**
     * Return a JSON serializable representation of the current state
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->_getValues(512, [], true);
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

        return $this->_getValues($depth, $except, false);
    }

    /**
     * Get a representation of the current state
     *
     * @param array $except                provide a list of properties which shouldn't be contained in the resulting JSON.
     *                                     eg. if you want to return an user model and don't want the password to be included
     * @param int $depth                   the maximum level of object nesting. Must be greater than 0
     * @param bool $emptyObjectsAsStdClass If set to true, the wrapping data structure for empty objects will be an stdClass. Array otherwise
     *
     * @return array|stdClass
     */
    private function _getValues(int $depth, array $except, bool $emptyObjectsAsStdClass)
    {
        $depth--;
        $modelData = [];

        if (isset($this->_skipNotProvidedPropertiesMap, $this->_rawModelDataInput)) {
            $except = array_merge(
                $except,
                array_diff($this->_skipNotProvidedPropertiesMap, array_keys($this->_rawModelDataInput))
            );
        }

        foreach (get_class_vars(get_class($this)) as $key => $value) {
            if (in_array($key, $except) || str_starts_with($key, '_') !== false) {
                continue;
            }

            if ($customSerializer = $this->_getCustomSerializerMethod($key)) {
                $modelData[$key] = $this->_getSerializedValue($this->{$customSerializer}(), $depth, $except, $emptyObjectsAsStdClass);
                continue;
            }

            $modelData[$key] = $this->_getSerializedValue($this->$key, $depth, $except, $emptyObjectsAsStdClass);
        }

        $data = $this->resolveSerializationHook($modelData, $depth, $except);

        if ($emptyObjectsAsStdClass && empty($data)) {
            $data = new stdClass();
        }

        return $data;
    }

    /**
     * Function can be overwritten by classes using the trait to hook into serialization
     */
    protected function resolveSerializationHook(array $data, int $depth, array $except): array
    {
        return $data;
    }

    private function _getSerializedValue($value, int $depth, array $except, bool $emptyObjectsAsStdClass = false) {
        if (is_array($value)) {
            $subData = [];
            foreach ($value as $subKey => $element) {
                $subData[$subKey] = $this->_getSerializedValue($element, $depth - 1, $except, $emptyObjectsAsStdClass);
            }
            return $subData;
        }

        return $this->evaluateAttribute($value, $depth, $except, $emptyObjectsAsStdClass);
    }

    private function evaluateAttribute($attribute, int $depth, array $except, bool $emptyObjectsAsStdClass)
    {
        if (!is_object($attribute)) {
            return $attribute;
        }

        if ($depth === 0 && method_exists($attribute, '__toString')) {
            return (string) $attribute;
        }

        $data = match (true) {
            0 >= $depth                                                           => null,
            $emptyObjectsAsStdClass && method_exists($attribute, 'jsonSerialize') => $attribute->jsonSerialize(),
            method_exists($attribute, 'toArray')                                  => $attribute->toArray(),
            default                                                               => get_object_vars($attribute),
        };

        if ($data === [] && $emptyObjectsAsStdClass) {
            $data = new stdClass();
        }

        return $data;
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
