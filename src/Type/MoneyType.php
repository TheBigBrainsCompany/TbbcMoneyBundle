<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Money\Currency;
use Money\Money;

/**
 * Stores Money in a single field, in the smallest unit (cents). eg "EUR 100"
 * Note that this is only useful if you don't intend to query on this.
 *
 * @example
 *
 * @author Philippe Le Van.
 */
class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSqlDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Money
    {
        if (is_null($value)) {
            return null;
        }

        [$currency, $amount] = explode(' ', (string) $value, 2);

        return new Money($amount, new Currency($currency));
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Money) {
            return $value->getCurrency().' '.$value->getAmount();
        }

        throw ConversionException::conversionFailed((string) $value, self::NAME);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
