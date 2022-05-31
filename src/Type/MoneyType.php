<?php
namespace Tbbc\MoneyBundle\Type;

use Money\Money;
use Money\Currency;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Stores Money in a single field, in the smallest unit (cents). eg "EUR 100"
 * Note that this is only useful if you don't intend to query on this.
 *
 * @example
 * @author Philippe Le Van.
 */
class MoneyType extends Type
{
    const NAME = 'money';

    /**
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return Money|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        list($currency, $amount) = explode(' ', $value, 2);

        return new Money((int) $amount, new Currency($currency));
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return null|string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Money) {
            return (string) $value->getCurrency().' '.$value->getAmount();
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
