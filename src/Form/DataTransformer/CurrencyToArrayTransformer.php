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
 * Transforms between a Currency and an array.
 * 
 * @implements DataTransformerInterface<Currency, array>
 */
class CurrencyToArrayTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     * 
     * @psalm-param Currency|null $value
     */
    public function transform(mixed $value): ?array
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
     * @psalm-param array|null $value
     */
    public function reverseTransform(mixed $value): ?Currency
    {
        if (null === $value) {
            return null;
        }

        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!isset($value['tbbc_name'])) {
            return null;
        }

        if (!is_string($value['tbbc_name'])) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ('' === $value['tbbc_name']) {
            throw new TransformationFailedException('name can not be an empty string');
        }

        try {
            return new Currency($value['tbbc_name']);
        } catch (InvalidArgumentException|TypeError $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
