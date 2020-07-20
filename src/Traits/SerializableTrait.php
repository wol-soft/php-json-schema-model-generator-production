<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Traits;

use PHPModelGenerator\Model\SerializedValue;

/**
 * Provide methods to serialize generated models
 *
 * Trait SerializableTrait
 *
 * @package PHPModelGenerator\Traits
 */
trait SerializableTrait
{
    private static $customSerializer = [];

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
        array_push($except, 'rawModelDataInput', 'errorRegistry');

        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, $except)) {
                continue;
            }

            if ($customSerializer = $this->getCustomSerializerMethod($key)) {
                $this->handleSerializedValue($modelData, $key, $this->{$customSerializer}());
                continue;
            }

            if (is_array($value)) {
                $subData = [];
                foreach ($value as $subKey => $element) {
                    $subData[$subKey] = $this->evaluateAttribute($element, $depth, $except);
                }
                $modelData[$key] = $subData;
            } else {
                $modelData[$key] = $this->evaluateAttribute($value, $depth, $except);
            }
        }

        return $modelData;
    }

    private function handleSerializedValue(array &$data, $key, $serializedValue): void
    {
        if ($serializedValue instanceof SerializedValue &&
            $serializedValue->getSerializationStrategy() === SerializedValue::STRATEGY_MERGE_VALUE
        ) {
            $data = array_merge($data, $serializedValue->getSerializedValue());

            return;
        }

        $data[$key] = $serializedValue;
    }

    private function evaluateAttribute($attribute, int $depth, array $except)
    {
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

    private function getCustomSerializerMethod(string $property) {
        if (isset(self::$customSerializer[$property])) {
            return self::$customSerializer[$property];
        }

        $customSerializer = 'serialize' . ucfirst($property);
        if (!method_exists($this, $customSerializer)) {
            $customSerializer = false;
        }

        return self::$customSerializer[$property] = $customSerializer;
    }
}
