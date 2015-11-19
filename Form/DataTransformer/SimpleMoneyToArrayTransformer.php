<?php

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Currency;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Transforms between a Money instance and an array.
 */
class SimpleMoneyToArrayTransformer extends MoneyToArrayTransformer
{
    protected $currency;

    /**
     * SimpleMoneyToArrayTransformer constructor.
     *
     * @param int $decimals
     */
    public function __construct($decimals)
    {
        parent::__construct($decimals);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $tab = parent::transform($value);
        if (!$tab) {
            return null;
        }
        unset($tab["tbbc_currency"]);

        return $tab;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            $value["tbbc_currency"] = new Currency($this->currency);
        }

        return parent::reverseTransform($value);
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
}
