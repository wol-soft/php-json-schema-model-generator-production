<?php

declare(strict_types=1);

namespace PHPModelGenerator\Interfaces;

use PHPModelGenerator\Accessor\Meta;

interface BuilderInterface
{
    public function meta(): Meta;

    /**
     * Set up a new instance of the class related to the builder with fully validated properties
     */
    public function validate(): JSONModelInterface;
}
