<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Money;

/**
 * Transforms between a Money instance and an array.
 */
class SimpleMoneyToArrayTransformer extends MoneyToArrayTransformer
{
    protected string $currency = '';

    public function __construct(int $decimals)
    {
        parent::__construct($decimals);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value): ?array
    {
        if (!$tab = parent::transform($value)) {
            return null;
        }

        unset($tab['tbbc_currency']);

        return $tab;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value): ?Money
    {
        if (is_array($value)) {
            $value['tbbc_currency'] = $this->currency;
        }

        return parent::reverseTransform($value);
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
