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
     * Get an array representation of the current state
     *
     *
     * @param array $except provide a list of properties which shouldn't be contained in the resulting JSON.
     *                      eg. if you want to return an user model and don't want the password to be included
     * @param int $depth    the maximum level of object nesting. Must be greater than 0
     *
     * @return array|false
     */
    public function toArray(array $except = [], int $depth = 512);

    /**
     * Get a JSON representation of the current state
     *
     *
     * @param array $except provide a list of properties which shouldn't be contained in the resulting JSON.
     *                      eg. if you want to return an user model and don't want the password to be included
     * @param int $options  Bitmask for json_encode
     * @param int $depth    the maximum level of object nesting. Must be greater than 0
     *
     * @return string|false
     */
    public function toJSON(array $except = [], int $options = 0, int $depth = 512);
}
