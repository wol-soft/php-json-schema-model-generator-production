<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception;

use Exception;
use JsonSerializable;
use PHPModelGenerator\Interfaces\SerializationInterface;
use PHPModelGenerator\Traits\SerializableTrait;

/**
 * Class JSONModelValidationException
 *
 * @package PHPModelGenerator\Exception
 */
abstract class JSONModelValidationException extends Exception implements JsonSerializable, SerializationInterface
{
    use SerializableTrait;
}
