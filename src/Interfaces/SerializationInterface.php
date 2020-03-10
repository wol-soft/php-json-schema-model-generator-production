<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Interfaces;

/**
 * Interface SerializationInterface
 *
 * @package PHPModelGenerator\Interfaces
 */
interface SerializationInterface
{
    /**
     * Converts the object into an array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Converts the object into a JSON serialized string
     *
     * @return string
     */
    public function toJSON(): string;
}
