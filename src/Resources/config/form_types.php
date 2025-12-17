<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tbbc\MoneyBundle\Form\Type\{CurrencyType, MoneyType, SimpleMoneyType};

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->defaults()
        ->private()
        ->autoconfigure();

    $services
        ->set(CurrencyType::class)
        ->arg('$currencyCodeList', '%tbbc_money.currencies%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->tag('form.type', [
            'alias' => 'tbbc_currency',
        ]);

    $services
        ->set(MoneyType::class)
        ->arg('$decimals', '%tbbc_money.decimals%')
        ->tag('form.type', [
            'alias' => 'tbbc_money',
        ]);

    $services
        ->set(SimpleMoneyType::class)
        ->arg('$decimals', '%tbbc_money.decimals%')
        ->arg('$currencyCodeList', '%tbbc_money.currencies%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->tag('form.type', [
            'alias' => 'tbbc_simple_money',
        ]);
};
