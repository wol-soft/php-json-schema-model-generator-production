<?php

declare(strict_types=1);

namespace PHPModelGenerator\Accessor;

/**
 * Read-only access to the unevaluated properties of a generated immutable model.
 *
 * For typed unevaluated properties a generated companion class extends this one with narrowed signatures.
 */
class ImmutableUnevaluatedPropertiesAccessor
{
    public function __construct(protected readonly array $unevaluatedProperties) {}

    public function get(string $key): mixed
    {
        return $this->unevaluatedProperties[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->unevaluatedProperties;
    }
}
