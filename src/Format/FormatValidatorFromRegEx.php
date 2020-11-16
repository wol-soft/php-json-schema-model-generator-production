<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Format;

/**
 * Class FormatFromRegEx
 *
 * @package PHPModelGenerator\Format
 */
class FormatValidatorFromRegEx implements FormatValidatorInterface
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    public static function validate(string $input, string $pattern = ''): bool
    {
        return preg_match($pattern, $input) === 1;
    }
}
