<?php

declare(strict_types=1);

namespace PHPModelGenerator\ValueObject;

use Stringable;

/**
 * Mutable wrapper for a string property that carries contentMediaType / contentEncoding metadata.
 *
 * Used when neither readOnly, writeOnly, nor global immutability applies.
 * The schema-defined media type and encoding are injected at construction time.
 */
class MediaString implements Stringable
{
    public function __construct(
        private string $value,
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

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
