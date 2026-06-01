<?php

declare(strict_types=1);

namespace PHPModelGenerator\Interfaces;

use PHPModelGenerator\Accessor\Meta;

/**
 * Interface JSONModelInterface
 *
 * @package PHPModelGenerator\Interfaces
 */
interface JSONModelInterface
{
    public function meta(): Meta;
}
