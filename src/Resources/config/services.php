<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
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

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();
    $services->defaults()
        ->private()
        ->autoconfigure();

    // === Parameters ===
    $parameters->set('tbbc_money.pair_manager.ratio_file_name', '%kernel.project_dir%/../data/tbbc_money/ratio_file_name.csv');

    // === Services ===
    $services->set(PairManager::class)
        ->public()
        ->arg('$storage', service('tbbc_money.pair.csv_storage'))
        ->arg('$currencyCodeList', '%tbbc_money.currencies%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->arg('$dispatcher', service('event_dispatcher'));

    $services->alias(PairManagerInterface::class, PairManager::class);
    $services->alias('tbbc_money.pair_manager', PairManager::class)->public();

    $services->set(PairHistoryManager::class)
        ->public()
        ->arg('$entityManager', service(EntityManagerInterface::class))
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%');

    $services->alias(PairHistoryManagerInterface::class, PairHistoryManager::class);
    $services->alias('tbbc_money.pair_history_manager', PairHistoryManager::class)->public();

    $services->set(MoneyManager::class)
        ->public()
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%')
        ->arg('$decimals', '%tbbc_money.decimals%');

    $services->alias('tbbc_money.money_manager', MoneyManager::class)->public();

    // Storage
    $services->set(CsvStorage::class)
        ->arg('$fileName', '%tbbc_money.pair_manager.ratio_file_name%')
        ->arg('$referenceCurrencyCode', '%tbbc_money.reference_currency%');

    $services->alias('tbbc_money.pair.csv_storage', CsvStorage::class);

    // Ratio providers
    $services->set(ECBRatioProvider::class)
        ->arg('$httpClient', service(HttpClientInterface::class));

    $services->alias('tbbc_money.ratio_provider.ecb', ECBRatioProvider::class);

    // Formatter
    $services->set(MoneyFormatter::class)
        ->public()
        ->arg('$decimals', '%tbbc_money.decimals%');

    $services->alias('tbbc_money.formatter.money_formatter', MoneyFormatter::class)->public();

    // Commands
    $services->set(RatioFetchCommand::class)
        ->arg('$pairManager', service(PairManager::class));

    $services->set(RatioListCommand::class)
        ->arg('$pairManager', service(PairManager::class));

    $services->set(RatioSaveCommand::class)
        ->arg('$pairManager', service(PairManager::class));
};
