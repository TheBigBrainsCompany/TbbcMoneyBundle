<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->private()
        ->autoconfigure();

    $services->set(MoneyExtension::class)
        ->arg('$moneyFormatter', service(MoneyFormatter::class))
        ->arg('$pairManager', service(PairManagerInterface::class));

    $services->set(CurrencyExtension::class)
        ->arg('$moneyFormatter', service(MoneyFormatter::class));
};
