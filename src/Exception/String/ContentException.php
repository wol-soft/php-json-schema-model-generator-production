<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\String;

use PHPModelGenerator\Exception\ValidationException;
use Throwable;

class ContentException extends ValidationException
{
    public function __construct(
        $providedValue,
        string $propertyName,
        protected ?string $expectedMediaType,
        protected ?string $expectedEncoding,
        ?Throwable $previous = null,
    ) {
        $description = implode(', ', array_filter([
            $this->expectedMediaType ? "mediaType {$this->expectedMediaType}" : null,
            $this->expectedEncoding ? "encoding {$this->expectedEncoding}" : null,
        ]));

        parent::__construct(
            "Value for $propertyName does not match the expected content ($description)",
            $propertyName,
            $providedValue,
            0,
            $previous,
        );
    }

    public function getExpectedMediaType(): ?string
    {
        return $this->expectedMediaType;
    }

    public function getExpectedEncoding(): ?string
    {
        return $this->expectedEncoding;
    }
}
