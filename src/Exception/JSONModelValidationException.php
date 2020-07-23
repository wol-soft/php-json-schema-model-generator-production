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
    use SerializableTrait {
        toArray as protected _toArray;
    }

    /**
     * @inheritDoc
     *
     * @param bool $stripSensitiveData By default the file and the line of the exception will not be serialized
     */
    public function toArray(array $except = [], int $depth = 512, bool $stripSensitiveData = true)
    {
        if ($stripSensitiveData && !in_array('__KEEP_SENSITIVE_DATA__', $except)) {
            $except = array_merge($except, ['file', 'line']);
        } else {
            array_push($except, '__KEEP_SENSITIVE_DATA__');
        }

        return $this->_toArray($except, $depth);
    }

    /**
     * @inheritDoc
     */
    public function toJSON(array $except = [], int $options = 0, int $depth = 512, bool $stripSensitiveData = true)
    {
        if ($depth < 1) {
            return false;
        }

        return json_encode($this->toArray($except, $depth, $stripSensitiveData), $options, $depth);
    }
}
