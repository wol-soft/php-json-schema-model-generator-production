<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

/**
 * Class AnyOfException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
class AnyOfException extends InvalidComposedValueException
{
    protected const COMPOSED_ERROR_MESSAGE = 'Requires to match at least one composition element.';
}
