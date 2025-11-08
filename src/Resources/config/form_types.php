<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tbbc\MoneyBundle\Form\Type\{MoneyType, CurrencyType, SimpleMoneyType};

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(CurrencyType::class)
        ->args(['%tbbc_money.currencies%', '%tbbc_money.reference_currency%'])
        ->tag('form.type', ['alias' => 'tbbc_currency']);

    $services->set(MoneyType::class)
        ->args(['%tbbc_money.decimals%'])
        ->tag('form.type', ['alias' => 'tbbc_money']);

    $services->set(SimpleMoneyType::class)
        ->args(['%tbbc_money.decimals%', '%tbbc_money.currencies%', '%tbbc_money.reference_currency%'])
        ->tag('form.type', ['alias' => 'tbbc_simple_money']);
};