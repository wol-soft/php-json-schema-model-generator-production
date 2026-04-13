<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class AdditionalTupleItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class AdditionalTupleItemsException extends ValidationException
{
    /**
     * AdditionalTupleItemsException constructor.
     *
     * @param        $providedValue
     */
    public function __construct($providedValue, string $propertyName, protected int $expectedAmount, protected int $amount)
    {
        parent::__construct(
            sprintf(
                'Tuple array %s contains not allowed additional items. Expected %s items, got %s',
                $propertyName,
                $this->expectedAmount,
                $this->amount
            ),
            $propertyName,
            $providedValue
        );
    }

    public function getExpectedAmount(): int
    {
        return $this->expectedAmount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
