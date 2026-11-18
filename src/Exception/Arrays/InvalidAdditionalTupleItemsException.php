<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\Object\InvalidAdditionalPropertiesException;

class InvalidAdditionalTupleItemsException extends InvalidAdditionalPropertiesException
{
    protected const MAIN_MESSAGE = "Tuple array '%s' contains invalid additional items";
    protected const TYPE = 'additional item';
    protected const PROPERTY_NAME_IS_PLACEHOLDER = false;
}
