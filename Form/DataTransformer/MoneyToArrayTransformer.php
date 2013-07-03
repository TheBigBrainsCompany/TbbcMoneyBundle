<?php

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Transforms between a Money instance and an array.
 */
class MoneyToArrayTransformer implements DataTransformerInterface
{
    /** @var  MoneyToLocalizedStringTransformer */
    protected $sfTransformer;

    public function __construct()
    {
        $this->sfTransformer = new MoneyToLocalizedStringTransformer(null, null, null, 100);
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new UnexpectedTypeException($value, 'Money');
        }

        $amount = $this->sfTransformer->transform($value->getAmount());

        return array(
            'tbbc_amount' => $amount,
            'tbbc_currency' => $value->getCurrency()
        );
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }
        if (!isset($value['tbbc_amount']) || !isset($value['tbbc_currency'])) {
            return null;
        }
        $amount = (string)$value['tbbc_amount'];
        $amount = $this->sfTransformer->reverseTransform($amount);
        $amount = round($amount);
        $amount = (int)$amount;

        return new Money($amount, $value['tbbc_currency']);
    }

}
