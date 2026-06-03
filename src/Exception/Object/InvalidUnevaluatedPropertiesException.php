<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Object;

/**
 * Class InvalidUnevaluatedPropertiesException
 *
 * @package PHPModelGenerator\Exception\Object
 */
class InvalidUnevaluatedPropertiesException extends InvalidAdditionalPropertiesException
{
    protected const MAIN_MESSAGE = 'Provided JSON for %s contains invalid unevaluated properties.';
    protected const TYPE = 'unevaluated property';
}
