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
class CurrencyToArrayTransformer implements DataTransformerInterface
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
        return array("tbbc_name" => $value->getName());
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
        if (!isset($value["tbbc_name"])) {
            return null;
        }
        try {
            return new Currency($value["tbbc_name"]);
        } catch (UnknownCurrencyException $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
