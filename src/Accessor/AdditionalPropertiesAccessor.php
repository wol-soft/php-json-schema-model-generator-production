<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

/**
 * Mutable access to the additional properties of a generated model.
 *
 * For typed additional properties a generated companion class extends this one with narrowed signatures.
 */
class AdditionalPropertiesAccessor
{
    public function __construct(
        protected array &$additionalProperties,
        private readonly \Closure $setter,
        private readonly \Closure $remover,
    ) {}

    public function get(string $key): mixed
    {
        return $this->additionalProperties[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->additionalProperties;
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
