<?php

declare(strict_types=1);

namespace PHPModelGenerator\Filter;

interface FilterInterface
{
    /**
     * Return the token for the filter
     */
    public function getToken(): string;

    /**
     * Return the filter to apply. Make sure the returned array is a callable which is also callable after the
     * render process
     */
    public function getFilter(): array;
}
