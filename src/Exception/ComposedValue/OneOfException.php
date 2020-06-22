<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

/**
 * Class OneOfException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
class OneOfException extends InvalidComposedValueException
{
    protected const COMPOSED_ERROR_MESSAGE = 'Requires to match one composition element but matched %s elements.';
}
