<?php

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Currency;
use Money\UnknownCurrencyException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a Currency and a string
 */
class CurrencyToStringTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof Currency) {
            throw new UnexpectedTypeException($value, 'Currency');
        }
        return $value->getName();
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }
        try {
            return new Currency($value);
        } catch (UnknownCurrencyException $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
