<?php
/**
 * Created by IntelliJ IDEA.
 * User: sowhat
 */

namespace Tbbc\MoneyBundle\Utils;


use Money\Currency;

class CurrencyUtils
{
    /**
     * @param mixed $var
     * @return bool
     */
    public static function isCurrency($var)
    {
        return is_object($var) and $var instanceof Currency;
    }

    public static function isInCurrencyCodeFormat($code)
    {
        return is_string($code) and 3 === strlen($code);
    }
}