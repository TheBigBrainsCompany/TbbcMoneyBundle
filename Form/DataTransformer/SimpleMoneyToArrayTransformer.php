<?php

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Currency;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Transforms between a Money instance and an array.
 */
class SimpleMoneyToArrayTransformer
    extends MoneyToArrayTransformer
{
    /** @var  PairManagerInterface */
    protected $pairManager;
    public function __construct(PairManagerInterface $pairManager, $decimals)
    {
        parent::__construct($decimals);
        $this->pairManager = $pairManager;
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        $tab = parent::transform($value);
        if (!$tab) {
            return null;
        }
        unset ($tab["tbbc_currency"]);
        return $tab;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            $value["tbbc_currency"] = new Currency($this->pairManager->getReferenceCurrencyCode());
        }
        return parent::reverseTransform($value);
    }

}
