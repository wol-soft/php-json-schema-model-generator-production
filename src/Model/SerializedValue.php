<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Model;

use Exception;

class SerializedValue
{
    // simply add the provided value under the property key
    public const STRATEGY_ADD_VALUE = 0;
    // merge the provided value into the serialization result
    public const STRATEGY_MERGE_VALUE = 1;

    /** @var mixed */
    protected $serializedValue;
    /** @var int */
    protected $serializationStrategy;

    public function __construct($serializedValue, int $serializationStrategy = self::STRATEGY_ADD_VALUE)
    {
        if (!is_array($serializedValue) && $serializationStrategy === self::STRATEGY_MERGE_VALUE) {
            throw new Exception("Can't merge a non-array value into serialization result");
        }

        $this->serializedValue = $serializedValue;
        $this->serializationStrategy = $serializationStrategy;
    }

    /**
     * @return mixed
     */
    public function getSerializedValue()
    {
        return $this->serializedValue;
    }

    /**
     * @return int
     */
    public function getSerializationStrategy(): int
    {
        return $this->serializationStrategy;
    }
}
