<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception;

use PHPModelGenerator\Attributes\JsonPointer;
use Throwable;

/**
 * Class ValidationException
 *
 * @package PHPModelGeneratorException
 */
abstract class ValidationException extends JSONModelValidationException
{
    private string $instancePointerSegment;
    private ?ValidationException $instancePointerParent = null;

    /**
     * ValidationException constructor.
     *
     * @param int $code
     */
    public function __construct(
        string $message,
        protected string $propertyName,
        protected mixed $providedValue,
        protected string $jsonPointer,
        $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->instancePointerSegment = '/' . self::escapeInstancePointerSegment($propertyName);
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @return mixed
     */
    public function getProvidedValue()
    {
        return $this->providedValue;
    }

    /**
     * The JSON pointer to the schema location of the constraint that rejected the value.
     */
    public function getJsonPointer(): JsonPointer
    {
        return new JsonPointer($this->jsonPointer);
    }

    /**
     * The JSON pointer (RFC 6901) to this value's location within the validated data instance
     * (e.g. "/goals/0/kind"), as opposed to getJsonPointer() which points into the schema.
     *
     * Resolved lazily by walking the parent chain set up via setInstancePointerParent(), so a
     * wrapper added after this exception was constructed (e.g. an array item later found to be
     * nested inside another array) is still reflected correctly.
     */
    public function getInstancePointer(): JsonPointer
    {
        return new JsonPointer(
            ($this->instancePointerParent?->getInstancePointer()->pointer ?? '') . $this->instancePointerSegment
        );
    }

    /**
     * Link this exception's instance pointer to continue from an enclosing exception's, as
     * exceptions bubble up from the point of failure to the root. Called by wrapping exceptions
     * (InvalidItemException, InvalidTupleException, InvalidAdditionalPropertiesException,
     * InvalidPatternPropertiesException, NestedObjectException, InvalidComposedValueException,
     * ConditionalException) on the nested exceptions they collect.
     *
     * Pass $segment when this exception's own propertyName is a placeholder shared by every
     * instance of an enclosing collection (an array item, an additional/pattern property) rather
     * than this value's real position — it replaces the auto-seeded segment with the caller's
     * actual position (an array index, the real property key). Omit it when this exception's own
     * propertyName is already a real, distinct name (composition branches, conditional branches,
     * and nested object properties all keep their own name and only need the parent link).
     */
    public function setInstancePointerParent(ValidationException $parent, int|string|null $segment = null): void
    {
        $this->instancePointerParent = $parent;

        if ($segment !== null) {
            $this->instancePointerSegment = '/' . self::escapeInstancePointerSegment((string) $segment);
        }
    }

    /**
     * Clear this exception's own instance pointer segment, leaving only whatever a parent link
     * (set via setInstancePointerParent()) contributes. Used by exceptions whose propertyName is
     * a class-name placeholder rather than a real instance-path segment: base validators that
     * validate the current object itself (additionalProperties/patternProperties checks) run
     * inside the generated class's own code, with no access to the property name their parent
     * used to reach this object, so they fall back to the class name purely for message text.
     */
    protected function suppressOwnInstancePointerSegment(): void
    {
        $this->instancePointerSegment = '';
    }

    private static function escapeInstancePointerSegment(string $segment): string
    {
        return str_replace(['~', '/'], ['~0', '~1'], $segment);
    }
}
