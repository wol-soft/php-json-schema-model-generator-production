<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\Generic\InvalidAdditionalPropertiesException;

/**
 * Class InvalidAdditionalItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class InvalidAdditionalItemsException extends InvalidAdditionalPropertiesException
{
    protected const MAIN_MESSAGE = 'Tuple array %s contains invalid additional items.';
    protected const TYPE = 'additional item';
}
