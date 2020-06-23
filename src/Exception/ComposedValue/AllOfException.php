<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\ComposedValue;

/**
 * Class AllOfException
 *
 * @package PHPModelGenerator\Exception\ComposedValue
 */
class AllOfException extends InvalidComposedValueException
{
    protected const COMPOSED_ERROR_MESSAGE = 'Requires to match all composition elements but matched %s elements.';
}
