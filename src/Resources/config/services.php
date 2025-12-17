<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
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
    $services->defaults()->public();

    // === Parameters ===
    $parameters->set('tbbc_money.pair_manager.ratio_file_name', '%kernel.project_dir%/../data/tbbc_money/ratio_file_name.csv');

    // === Services ===
    $services->set('tbbc_money.pair_manager', PairManager::class)
        ->args([
            new Reference('tbbc_money.pair.csv_storage'),
            '%tbbc_money.currencies%',
            '%tbbc_money.reference_currency%',
            new Reference('event_dispatcher'),
        ]);

    $services->alias(PairManagerInterface::class, 'tbbc_money.pair_manager')->private();

    $services->set('tbbc_money.pair_history_manager', PairHistoryManager::class)
        ->args([
            new Reference(EntityManagerInterface::class),
            '%tbbc_money.reference_currency%',
        ]);

    $services->alias(PairHistoryManagerInterface::class, 'tbbc_money.pair_history_manager')->private();

    $services->set('tbbc_money.money_manager', MoneyManager::class)
        ->args([
            '%tbbc_money.reference_currency%',
            '%tbbc_money.decimals%',
        ]);

    $services->alias(MoneyManager::class, 'tbbc_money.money_manager')->private();

    // Storage
    $services->set('tbbc_money.pair.csv_storage', CsvStorage::class)
        ->args([
            '%tbbc_money.pair_manager.ratio_file_name%',
            '%tbbc_money.reference_currency%',
        ]);

    // Ratio providers
    $services->set('tbbc_money.ratio_provider.ecb', ECBRatioProvider::class)
        ->args([
            new Reference(HttpClientInterface::class),
        ]);

    // Formatter
    $services->set('tbbc_money.formatter.money_formatter', MoneyFormatter::class)
        ->args([
            '%tbbc_money.decimals%',
        ]);

    $services->alias(MoneyFormatter::class, 'tbbc_money.formatter.money_formatter')->private();

    // Commands
    $services->set('tbbc_money.command.ratio_fetch', RatioFetchCommand::class)
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-fetch',
        ]);

    $services->set('tbbc_money.command.ratio_list', RatioListCommand::class)
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-list',
        ]);

    $services->set('tbbc_money.command.ratio_save', RatioSaveCommand::class)
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-save',
        ]);
};
