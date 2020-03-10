<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Interfaces;

/**
 * Interface JSONModelInterface
 *
 * @package PHPModelGenerator\Interfaces
 */
interface JSONModelInterface
{
    /**
     * Get the raw input used to set up the model
     *
     * @return array
     */
    public function getRawModelDataInput(): array;
}
