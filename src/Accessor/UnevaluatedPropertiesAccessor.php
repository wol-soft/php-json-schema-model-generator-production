<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

use Closure;

/**
 * Mutable access to the unevaluated properties of a generated model.
 *
 * The unevaluatedProperties keyword (Draft 2019-09 / 2020-12) captures keys that were not claimed
 * by any positive sibling applicator at the same schema level (properties, patternProperties,
 * additionalProperties, or a successful composition branch). Keys reaching this accessor satisfy
 * the keyword's subschema constraint.
 *
 * For typed unevaluated properties a generated companion class extends this one with narrowed signatures.
 */
class UnevaluatedPropertiesAccessor
{
    public function __construct(
        protected array &$unevaluatedProperties,
        private readonly Closure $setter,
        private readonly Closure $remover,
    ) {}

    public function get(string $key): mixed
    {
        return $this->unevaluatedProperties[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->unevaluatedProperties;
    }

    public function set(string $key, mixed $value): void
    {
        ($this->setter)($key, $value);
    }

    public function remove(string $key): bool
    {
        return ($this->remover)($key);
    }
}
