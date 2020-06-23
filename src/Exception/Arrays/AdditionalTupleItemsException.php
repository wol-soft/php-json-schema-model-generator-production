<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Arrays;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class AdditionalTupleItemsException
 *
 * @package PHPModelGenerator\Exception\Arrays
 */
class AdditionalTupleItemsException extends ValidationException
{
    /** @var int */
    protected $expectedAmount;
    /** @var int */
    protected $amount;

    /**
     * AdditionalTupleItemsException constructor.
     *
     * @param        $providedValue
     * @param string $propertyName
     * @param int    $expectedAmount
     * @param int    $amount
     */
    public function __construct($providedValue, string $propertyName, int $expectedAmount, int $amount)
    {
        $this->expectedAmount = $expectedAmount;
        $this->amount = $amount;

        parent::__construct(
            sprintf(
                'Tuple array %s contains not allowed additional items. Expected %s items, got %s',
                $propertyName,
                $expectedAmount,
                $amount
            ),
            $propertyName,
            $providedValue
        );
    }

    /**
     * @return int
     */
    public function getExpectedAmount(): int
    {
        return $this->expectedAmount;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }
}
