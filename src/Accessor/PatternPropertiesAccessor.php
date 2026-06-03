<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

use PHPModelGenerator\Exception\Object\UnknownPatternPropertyException;

/**
 * Read-only access to the pattern properties of a generated model.
 *
 * A reference to the backing array is held so that changes made through the model
 * (e.g. removal of an additional property that matched a pattern) are immediately
 * visible through this accessor.
 */
class PatternPropertiesAccessor
{
    public function __construct(protected array &$patternProperties) {}

    /**
     * @throws UnknownPatternPropertyException
     */
    public function get(string $key): array
    {
        $hash = md5($key);

        if (!isset($this->patternProperties[$hash])) {
            throw new UnknownPatternPropertyException("Tried to access unknown pattern properties with key $key");
        }

        return $this->patternProperties[$hash];
    }
}
