<?php

declare(strict_types=1);

namespace PHPModelGenerator\ValueObject;

use Stringable;

/**
 * Immutable wrapper for a string property that carries contentMediaType / contentEncoding metadata.
 *
 * Used when readOnly: true, writeOnly: true, or global immutability is active.
 * The schema-defined media type and encoding are injected at construction time.
 */
class ImmutableMediaString implements Stringable
{
    public function __construct(
        private readonly string $value,
        private readonly ?string $mediaType = null,
        private readonly ?string $encoding = null,
    ) {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
