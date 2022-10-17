<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Filter;

interface ValidateOptionsInterface
{
    /**
     * Implement validations against the provided options array.
     * The validation will be executed during the model generation process.
     * Simply throw an exception if the provided options are invalid for your custom filter
     * (e.g. missing required option)
     */
    public function validateOptions(array $options): void;
}
