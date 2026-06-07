<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

class AnyOfException extends InvalidComposedValueException
{
    protected const COMPOSED_ERROR_MESSAGE = 'Requires to match at least one composition element.';
    protected const COMPOSED_KEYWORD = 'anyOf';
}
