<?php

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a Money instance and an array.
 */
class MoneyToArrayTransformer implements DataTransformerInterface
{
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

        return array(
            'tbbc_amount' => $value->getAmount(),
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

        return new Money($value['tbbc_amount'], $value['tbbc_currency']);
    }

}
