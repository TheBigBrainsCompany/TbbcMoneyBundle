<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Money;

/**
 * Transforms between a Money and an array.
 */
class SimpleMoneyToArrayTransformer extends MoneyToArrayTransformer
{
    protected string $currency = '';

    /**
     * @return array{tbbc_amount: string}|null
     */
    public function transform(mixed $value): ?array
    {
        if (!$tab = parent::transform($value)) {
            return null;
        }

        unset($tab['tbbc_currency']);

        return $tab;
    }

    public function reverseTransform(mixed $value): ?Money
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
