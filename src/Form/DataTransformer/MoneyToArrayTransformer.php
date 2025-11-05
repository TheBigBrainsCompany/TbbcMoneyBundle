<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Form\DataTransformer;

use Money\Currency;
use Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

/**
 * Transforms between a Money and an array.
 * 
 * @implements DataTransformerInterface<Money, array>
 */
class MoneyToArrayTransformer implements DataTransformerInterface
{
    protected MoneyToLocalizedStringTransformer $sfTransformer;

    public function __construct(protected int $decimals = 2)
    {
        $this->sfTransformer = new MoneyToLocalizedStringTransformer($decimals, null, null, 10 ** $this->decimals);
    }

    /**
     * {@inheritdoc}
     * 
     * @psalm-param Money|null $value
     * 
     * @psalm-return array{tbbc_amount: string, tbbc_currency: Currency}|array{tbbc_amount: string}|null
     */
    public function transform(mixed $value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Money) {
            throw new UnexpectedTypeException($value, 'Money');
        }

        $amount = $this->sfTransformer->transform((float) $value->getAmount());

        return [
            'tbbc_amount' => $amount,
            'tbbc_currency' => $value->getCurrency(),
        ];
    }

    /**
     * {@inheritdoc}
     * 
     * @psalm-param array|null $value
     */
    public function reverseTransform(mixed $value): ?Money
    {
        if (null === $value) {
            return null;
        }

        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (!isset($value['tbbc_amount']) || !isset($value['tbbc_currency'])) {
            return null;
        }

        $amount = (string) $value['tbbc_amount'];
        $amount = str_replace(' ', '', $amount);
        $amount = (float) $this->sfTransformer->reverseTransform($amount);
        $amount = round($amount);
        $amount = (int) $amount;

        /** @var string|Currency $currency */
        $currency = $value['tbbc_currency'];

        if ('' === $currency) {
            throw new TransformationFailedException('currency can not be an empty string');
        }

        if (!$currency instanceof Currency) {
            $currency = new Currency($currency);
        }

        return new Money($amount, $currency);
    }
}
