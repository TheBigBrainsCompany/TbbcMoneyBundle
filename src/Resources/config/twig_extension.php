<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();
    $services->defaults()->public();
    $services->defaults()->autoconfigure();

    // === Parameters ===
    $parameters->set('tbbc_money.twig.money.class', MoneyExtension::class);
    $parameters->set('tbbc_money.twig.currency.class', CurrencyExtension::class);

    // === Services ===
    $services->set('tbbc_money.twig.money', '%tbbc_money.twig.money.class%')
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
            new Reference('tbbc_money.pair_manager'),
        ]);

    $services->set('tbbc_money.twig.currency', '%tbbc_money.twig.currency.class%')
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
        ]);
};
