<?php

use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Money\MoneyManager;
use Tbbc\MoneyBundle\PairHistory\PairHistoryManager;
use Tbbc\MoneyBundle\PairHistory\PairHistoryManagerInterface;
use Tbbc\MoneyBundle\Pair\Storage\CsvStorage;
use Tbbc\MoneyBundle\Pair\RatioProvider\ECBRatioProvider;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Command\RatioFetchCommand;
use Tbbc\MoneyBundle\Command\RatioListCommand;
use Tbbc\MoneyBundle\Command\RatioSaveCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();
    $services->defaults()->public();

    // === Parameters ===
    $parameters->set('tbbc_money.pair_manager.class', PairManager::class);
    $parameters->set('tbbc_money.pair_manager_interface.class', PairManagerInterface::class);
    $parameters->set('tbbc_money.money_manager.class', MoneyManager::class);
    $parameters->set('tbbc_money.pair_history_manager.class', PairHistoryManager::class);
    $parameters->set('tbbc_money.pair_history_manager_interface.class', PairHistoryManagerInterface::class);
    $parameters->set('tbbc_money.pair.csv_storage.class', CsvStorage::class);
    $parameters->set('tbbc_money.pair_manager.ratio_file_name', '%kernel.project_dir%/../data/tbbc_money/ratio_file_name.csv');
    $parameters->set('tbbc_money.ratio_provider.ecb.class', ECBRatioProvider::class);
    $parameters->set('tbbc_money.formatter.money_formatter.class', MoneyFormatter::class);
    $parameters->set('tbbc_money.command.ratio_fetch.class', RatioFetchCommand::class);
    $parameters->set('tbbc_money.command.ratio_list.class', RatioListCommand::class);
    $parameters->set('tbbc_money.command.ratio_save.class', RatioSaveCommand::class);

    // === Services ===
    $services->set('tbbc_money.pair_manager', '%tbbc_money.pair_manager.class%')
        ->args([
            new Reference('tbbc_money.pair.csv_storage'),
            '%tbbc_money.currencies%',
            '%tbbc_money.reference_currency%',
            new Reference('event_dispatcher'),
        ]);

    $services->alias('%tbbc_money.pair_manager_interface.class%', 'tbbc_money.pair_manager')->private();

    $services->set('tbbc_money.pair_history_manager', '%tbbc_money.pair_history_manager.class%')
        ->args([
            new Reference(EntityManagerInterface::class),
            '%tbbc_money.reference_currency%',
        ]);

    $services->alias('%tbbc_money.pair_history_manager_interface.class%', 'tbbc_money.pair_history_manager')->private();

    $services->set('tbbc_money.money_manager', '%tbbc_money.money_manager.class%')
        ->args([
            '%tbbc_money.reference_currency%',
            '%tbbc_money.decimals%',
        ]);

    $services->alias('%tbbc_money.money_manager.class%', 'tbbc_money.money_manager')->private();

    // Storage
    $services->set('tbbc_money.pair.csv_storage', '%tbbc_money.pair.csv_storage.class%')
        ->args([
            '%tbbc_money.pair_manager.ratio_file_name%',
            '%tbbc_money.reference_currency%',
        ]);

    // Ratio providers
    $services->set('tbbc_money.ratio_provider.ecb', '%tbbc_money.ratio_provider.ecb.class%')
        ->args([
            new Reference(HttpClientInterface::class),
        ]);

    // Formatter
    $services->set('tbbc_money.formatter.money_formatter', '%tbbc_money.formatter.money_formatter.class%')
        ->args([
            '%tbbc_money.decimals%',
        ]);

    $services->alias('%tbbc_money.formatter.money_formatter.class%', 'tbbc_money.formatter.money_formatter')->private();

    // Commands
    $services->set('tbbc_money.command.ratio_fetch', '%tbbc_money.command.ratio_fetch.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-fetch',
        ]);

    $services->set('tbbc_money.command.ratio_list', '%tbbc_money.command.ratio_list.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-list',
        ]);

    $services->set('tbbc_money.command.ratio_save', '%tbbc_money.command.ratio_save.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', [
            'command' => 'tbbc:money:ratio-save',
        ]);
};
