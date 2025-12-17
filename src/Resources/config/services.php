<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tbbc\MoneyBundle\Command\RatioFetchCommand;
use Tbbc\MoneyBundle\Command\RatioListCommand;
use Tbbc\MoneyBundle\Command\RatioSaveCommand;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Money\MoneyManager;
use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Pair\RatioProvider\ECBRatioProvider;
use Tbbc\MoneyBundle\Pair\Storage\CsvStorage;
use Tbbc\MoneyBundle\PairHistory\PairHistoryManager;
use Tbbc\MoneyBundle\PairHistory\PairHistoryManagerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();
    $services->defaults()->public();
    $services->defaults()
        ->private()
        ->autoconfigure();

    $parameters->set('tbbc_money.pair_manager.ratio_file_name', '%kernel.project_dir%/../data/tbbc_money/ratio_file_name.csv');

    $services->set(PairManagerInterface::class, PairManager::class)
        ->arg('$storage', service(CsvStorage::class))
        ->arg('$currencyCodeList', '%tbbc_money.currencies%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->arg('$dispatcher', service('event_dispatcher'));
    $services->alias(PairManager::class, PairManagerInterface::class);

    $services->set(PairHistoryManagerInterface::class, PairHistoryManager::class)
        ->arg('$em', service(EntityManagerInterface::class))
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%');
    $services->alias(PairHistoryManager::class, PairHistoryManagerInterface::class);

    $services->set(MoneyManager::class)
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->arg('$decimals', '%tbbc_money.decimals%');

    $services->set(CsvStorage::class)
        ->arg('$ratioFileName', '%tbbc_money.pair_manager.ratio_file_name%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%');

    $services->set(ECBRatioProvider::class)
        ->arg('$client', service(HttpClientInterface::class));

    $services->set(MoneyFormatter::class)
        ->arg('$decimals', '%tbbc_money.decimals%');

    $services->set(RatioFetchCommand::class)
        ->arg('$pairManager', service(PairManagerInterface::class));

    $services->set(RatioListCommand::class)
        ->arg('$pairManager', service(PairManagerInterface::class));

    $services->set(RatioSaveCommand::class)
        ->arg('$pairManager', service(PairManagerInterface::class));
};
