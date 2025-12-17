<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->private()
        ->autoconfigure();

    // === Services ===
    $services->set(MoneyExtension::class)
        ->arg('$formatter', service(MoneyFormatter::class))
        ->arg('$pairManager', service(PairManager::class));

    $services->set(CurrencyExtension::class)
        ->arg('$formatter', service(MoneyFormatter::class));
};
