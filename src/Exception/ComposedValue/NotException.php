<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

class NotException extends InvalidComposedValueException
{
    protected const COMPOSED_ERROR_MESSAGE = 'Requires to match none composition element but matched %s elements.';
    protected const COMPOSED_KEYWORD = 'not';
}
