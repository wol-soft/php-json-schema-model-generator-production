<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

/**
 * Read-only access to the additional properties of a generated immutable model.
 *
 * For typed additional properties a generated companion class extends this one with narrowed signatures.
 */
class ImmutableAdditionalPropertiesAccessor
{
    public function __construct(protected readonly array $additionalProperties) {}

    public function get(string $key): mixed
    {
        return $this->additionalProperties[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->additionalProperties;
    }
}
