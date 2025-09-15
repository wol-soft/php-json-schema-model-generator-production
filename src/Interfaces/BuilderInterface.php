<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Interfaces;

interface BuilderInterface
{
    /**
     * Get the raw input used to set up the model
     */
    public function getRawModelDataInput(): array;

    /**
     * Set up a new instance of the class related to the builder with fully validated properties
     */
    public function validate(): JSONModelInterface;
}
