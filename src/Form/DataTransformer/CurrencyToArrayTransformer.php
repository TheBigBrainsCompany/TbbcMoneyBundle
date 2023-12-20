<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use InvalidArgumentException;
use Money\Currency;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use TypeError;

/**
 * Transforms between a Currency and a string.
 */
class CurrencyToArrayTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }
        if (!$value instanceof Currency) {
            throw new UnexpectedTypeException($value, 'Currency');
        }

        return ['tbbc_name' => $value->getCode()];
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function reverseTransform($value): ?Currency
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!isset($value['tbbc_name'])) {
            return null;
        }

        try {
            return new Currency($value['tbbc_name']);
        } catch (InvalidArgumentException|TypeError $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
