<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

/**
 * Class InvalidPropertyNamesException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidPropertyNamesException extends InvalidAdditionalPropertiesException
{
    protected const MAIN_MESSAGE = 'Provided JSON for %s contains properties with invalid names.';
    protected const TYPE = 'property';
}
