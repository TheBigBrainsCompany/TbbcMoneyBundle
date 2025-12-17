<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()->public();
    $services->defaults()->autoconfigure();

    $services->set('tbbc_money.twig.money', MoneyExtension::class)
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
            new Reference('tbbc_money.pair_manager'),
        ]);

    $services->set('tbbc_money.twig.currency', CurrencyExtension::class)
        ->args([
            new Reference('tbbc_money.formatter.money_formatter'),
        ]);
};
