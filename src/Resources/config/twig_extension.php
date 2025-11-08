<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();

    // === Parameters ===
    $parameters->set('tbbc_money.twig.money.class', Tbbc\MoneyBundle\Twig\Extension\MoneyExtension::class);
    $parameters->set('tbbc_money.twig.currency.class', Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension::class);

    // === Services ===
    $services->set('tbbc_money.twig.money', '%tbbc_money.twig.money.class%')
        ->public()
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
            new Reference('tbbc_money.pair_manager'),
        ])
        ->tag('twig.extension');

    $services->set('tbbc_money.twig.currency', '%tbbc_money.twig.currency.class%')
        ->public()
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
        ])
        ->tag('twig.extension');
};
