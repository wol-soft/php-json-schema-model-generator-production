<?php

declare(strict_types=1);

namespace PHPModelGenerator\Traits;

use PHPModelGenerator\Attributes\Internal;
use PHPModelGenerator\Attributes\SchemaName;
use PHPModelGenerator\Interfaces\SerializationInterface;
use ReflectionClass;
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
    /**
     * Maps concrete class names to their php-name => schema-name property maps.
     * Populated once per class via reflection.
     *
     * @var array<class-string, array<string, string>>
     */
    #[Internal]
    private static array $propertySchemaNames = [];

    /**
     * Maps concrete class names to their serialization capability string.
     * Populated once per encountered nested-object class.
     *
     * @var array<class-string, string>
     */
    #[Internal]
    private static array $objectSerializationCapability = [];

    /** @var array<string, string|false> */
    #[Internal]
    private static array $customSerializer = [];

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

        return json_encode($this->getValues($depth, $except, true), $options, $depth);
    }

    /**
     * Return a JSON serializable representation of the current state
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(array $except = [])
    {
        return $this->getValues(512, $except, true);
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

        return $this->getValues($depth, $except, false);
    }

    /**
     * Get a representation of the current state
     *
     * @param array $except                provide a list of properties which shouldn't be contained in the resulting
     *                                     JSON. eg. if you want to return an user model and don't want the password
     *                                     to be included
     * @param int $depth                   the maximum level of object nesting. Must be greater than 0
     * @param bool $emptyObjectsAsStdClass If set to true, the wrapping data structure for empty objects will be an
     *                                     stdClass. Array otherwise
     *
     * @return array|stdClass
     */
    private function getValues(int $depth, array $except, bool $emptyObjectsAsStdClass)
    {
        $depth--;
        $modelData = [];

        $localExcept = $except;
        if (isset($this->skipNotProvidedPropertiesMap, $this->rawModelDataInput)) {
            $localExcept = array_merge(
                $localExcept,
                array_diff($this->skipNotProvidedPropertiesMap, array_keys($this->rawModelDataInput))
            );
        }

        foreach ($this->getPropertySchemaNames() as $phpName => $schemaName) {
            if (in_array($schemaName, $localExcept, true)) {
                continue;
            }

            if ($customSerializer = $this->getCustomSerializerMethod($phpName)) {
                $modelData[$schemaName] = $this->getSerializedValue(
                    $this->{$customSerializer}(),
                    $depth,
                    $except,
                    $emptyObjectsAsStdClass,
                );
                continue;
            }

            $modelData[$schemaName] = $this->getSerializedValue(
                $this->{$phpName},
                $depth,
                $except,
                $emptyObjectsAsStdClass,
            );
        }

        $data = $this->resolveSerializationHook($modelData, $depth, $except);

        if ($emptyObjectsAsStdClass && empty($data)) {
            return new stdClass();
        }

        return $data;
    }

    /**
     * Build and cache the php-name => schema-name map for this concrete class in a single
     * reflection pass.
     *
     * For generated model classes every schema-derived property carries a #[SchemaName]
     * attribute; only those properties are included, keyed by their original JSON Schema name.
     *
     * For hand-written classes that include this trait (e.g. the exception hierarchy) no
     * #[SchemaName] attributes are present. In that case the method falls back to the legacy
     * behaviour and uses the PHP property name as both the key and the output key, preserving
     * backward-compatible serialization for those classes.
     *
     * Static properties and properties marked with #[Internal] are never serialized.
     *
     * @return array<string, string>
     */
    private function getPropertySchemaNames(): array
    {
        if (isset(self::$propertySchemaNames[static::class])) {
            return self::$propertySchemaNames[static::class];
        }

        $map = [];
        $hasSchemaNames = false;
        $fallbackMap = [];

        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            // Static properties are never part of an instance's serialized state.
            if ($property->isStatic()) {
                continue;
            }

            $phpName = $property->getName();
            $schemaNameAttributes = $property->getAttributes(SchemaName::class);

            if ($schemaNameAttributes !== []) {
                $map[$phpName] = $schemaNameAttributes[0]->newInstance()->name;
                $hasSchemaNames = true;
            } elseif (!$hasSchemaNames) {
                // Accumulate fallback entries for non-generated classes. Exclude any property
                // explicitly marked with #[Internal].
                if ($property->getAttributes(Internal::class) === []) {
                    $fallbackMap[$phpName] = $phpName;
                }
            }
        }

        return self::$propertySchemaNames[static::class] = $hasSchemaNames ? $map : $fallbackMap;
    }

    /**
     * Function can be overwritten by classes using the trait to hook into serialization
     */
    protected function resolveSerializationHook(array $data, int $depth, array $except): array
    {
        return $data;
    }

    private function getSerializedValue($value, int $depth, array $except, bool $emptyObjectsAsStdClass = false)
    {
        if (is_array($value)) {
            $subData = [];
            foreach ($value as $subKey => $element) {
                $subData[$subKey] = $this->getSerializedValue(
                    $element,
                    $depth - 1,
                    $except,
                    $emptyObjectsAsStdClass,
                );
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

        // Determine and cache the serialization capability of this concrete class.
        // The cache key is the class name only — capability is class-intrinsic.
        // Context-dependent decisions (depth, emptyObjectsAsStdClass) are handled
        // after the cache lookup so they never pollute the cached value.
        self::$objectSerializationCapability[$attribute::class] ??= match (true) {
            $attribute instanceof SerializationInterface => 'protocol',
            method_exists($attribute, 'jsonSerialize')   => 'jsonSerializable',
            method_exists($attribute, 'toArray')         => 'toArray',
            method_exists($attribute, '__toString')      => 'stringable',
            default                                      => 'plain',
        };

        // Depth-exhausted: return a string representation if possible, null otherwise.
        if ($depth <= 0) {
            return method_exists($attribute, '__toString') ? (string) $attribute : null;
        }

        $data = match (self::$objectSerializationCapability[$attribute::class]) {
            // Objects speaking our serialization protocol: propagate $except and $depth,
            // and choose the method that preserves the emptyObjectsAsStdClass semantics.
            'protocol' => $emptyObjectsAsStdClass
                ? $attribute->jsonSerialize($except)
                : $attribute->toArray($except, $depth),
            // Objects implementing JsonSerializable but not our protocol: we can pass
            // $except for the jsonSerialize branch only; no depth or $except for toArray
            // because we don't know the callee's signature.
            'jsonSerializable' => $emptyObjectsAsStdClass
                ? $attribute->jsonSerialize()
                : get_object_vars($attribute),
            'toArray' => $attribute->toArray(),
            // 'stringable' and 'plain' both fall through to get_object_vars; stringable
            // was handled by the depth-exhausted early return above.
            default => get_object_vars($attribute),
        };

        if ($data === [] && $emptyObjectsAsStdClass) {
            return new stdClass();
        }

        return $data;
    }

    private function getCustomSerializerMethod(string $property)
    {
        // Cache key includes the concrete class so that subclasses with additional
        // serialize*() methods are discovered independently of their parent class.
        // array_key_exists is used (not isset) because the cached "no serializer"
        // sentinel is false, and isset() returns false for null but true for false.
        $cacheKey = static::class . '::' . $property;
        if (array_key_exists($cacheKey, self::$customSerializer)) {
            return self::$customSerializer[$cacheKey];
        }

        $customSerializer = 'serialize' . ucfirst($property);
        if (!method_exists($this, $customSerializer)) {
            $customSerializer = false;
        }

        return self::$customSerializer[$cacheKey] = $customSerializer;
    }
}
