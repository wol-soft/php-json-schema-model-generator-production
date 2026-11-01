<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception;

use PHPModelGenerator\Attributes\JsonPointer;
use Throwable;

abstract class ValidationException extends JSONModelValidationException
{
    private string $instancePointerSegment;
    private string $nestedInstancePointerSegment;
    private ?ValidationException $instancePointerParent = null;

    /**
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
        $this->nestedInstancePointerSegment = $this->instancePointerSegment;
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
        return new JsonPointer($this->getEnclosingInstancePointer() . $this->instancePointerSegment);
    }

    /**
     * Mark this exception as raised by a validator of a generated class itself (e.g. minProperties,
     * additionalProperties: false or a composition keyword of an object). Such a validator runs
     * inside the generated class and has no access to the name its parent used to reach the
     * object, so the exception is constructed with the class name as property name. That name is
     * a label for the message text only: it is not part of the instance pointer, neither of this
     * exception nor of the exceptions nested below it. The pointer then continues at the enclosing
     * exception's pointer (the object property, or the root for the object being validated).
     *
     * Intended to be called right after the construction, e.g. `(new MinPropertiesException(...))
     * ->withPlaceholderPropertyName()`. A segment assigned afterwards by a wrapping exception (an
     * array index, an additional/pattern property key) is the real location of the value and takes
     * precedence; a segment assigned before this call is discarded. The exception classes are
     * also used by validators of a property, where the property name is real and kept: do not
     * call this method for those.
     */
    public function withPlaceholderPropertyName(): static
    {
        $this->suppressOwnInstancePointerSegment();

        return $this;
    }

    /**
     * Link this exception's instance pointer to continue from an enclosing exception's, as
     * exceptions bubble up from the point of failure to the root. Called by wrapping exceptions
     * (InvalidItemException, InvalidTupleException, InvalidAdditionalPropertiesException,
     * InvalidPatternPropertiesException, NestedObjectException, InvalidComposedValueException,
     * ConditionalException, InvalidSchemaDependencyException) on the nested exceptions they
     * collect.
     *
     * Pass $segment when this exception's own propertyName is a placeholder shared by every
     * instance of an enclosing collection (an array item, an additional/pattern property) rather
     * than this value's real position — it replaces the auto-seeded segment with the caller's
     * actual position (an array index, the real property key). Omit it when this exception's own
     * propertyName is already a real, distinct name (the properties of a nested object keep their
     * own name and only need the parent link). The exceptions of composition branches are linked
     * via linkBranchExceptions().
     */
    public function setInstancePointerParent(ValidationException $parent, int|string|null $segment = null): void
    {
        $this->instancePointerParent = $parent;

        if ($segment !== null) {
            $this->instancePointerSegment = '/' . self::escapeInstancePointerSegment((string) $segment);
            $this->nestedInstancePointerSegment = $this->instancePointerSegment;
        }
    }

    /**
     * Link the exceptions of a composition branch (a single exception or all errors of an error
     * registry) to this composition exception. A branch validates the same value as the
     * composition, and its validators raise their exceptions with the name of the composed
     * property. A branch exception with the same name as this exception is therefore located at
     * this exception's location and must not append its own, identical segment again (/name,
     * not /name/name); the exceptions nested below it follow (an item of a composed array is
     * located at /tags/0, not at /tags/tags/0). A branch exception with a different name is a
     * property of the composed object and keeps its segment (/obj/b).
     *
     * Deciding by the name instead of suppressing every branch exception is required for the
     * properties of an object: their distinct names are the only thing locating them. The
     * name check happens here, not in the branch exception itself, as that cannot know that it is
     * a branch. The comparison is not able to tell a branch exception from the exception
     * of an object property which carries the same name as this exception (a composition of a
     * generated class, named after the class, with a property named like the class).
     *
     * Call after the construction of this exception, the property name is required.
     */
    protected function linkBranchExceptions(?Throwable $branchException): void
    {
        $branchErrors = match (true) {
            $branchException instanceof ErrorRegistryExceptionInterface => $branchException->getErrors(),
            $branchException instanceof ValidationException => [$branchException],
            default => [],
        };

        foreach ($branchErrors as $branchError) {
            $branchError->setInstancePointerParent($this);

            if ($branchError->getPropertyName() === $this->propertyName) {
                $branchError->suppressOwnInstancePointerSegment();
            }
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
        $this->nestedInstancePointerSegment = '';
    }

    /**
     * Keep this exception's own segment for its own pointer, but resolve the exceptions nested
     * below it without that segment. For an exception which is located at a part of the value
     * (e.g. the property which triggers a dependency) while the exceptions nested below it were
     * raised for the enclosing value as a whole (the object owning that property): their pointers
     * continue at the enclosing pointer instead of being appended to this exception's segment.
     * Cleared again if a wrapper assigns the real position of the value via
     * setInstancePointerParent().
     */
    protected function excludeOwnInstancePointerSegmentFromNestedErrors(): void
    {
        $this->nestedInstancePointerSegment = '';
    }

    private function getEnclosingInstancePointer(): string
    {
        return $this->instancePointerParent?->getNestedInstancePointerBase() ?? '';
    }

    private function getNestedInstancePointerBase(): string
    {
        return $this->getEnclosingInstancePointer() . $this->nestedInstancePointerSegment;
    }

    private static function escapeInstancePointerSegment(string $segment): string
    {
        return str_replace(['~', '/'], ['~0', '~1'], $segment);
    }
}
