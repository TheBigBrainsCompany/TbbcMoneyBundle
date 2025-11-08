<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $services = $configurator->services();

    // === Parameters ===
    $parameters->set('tbbc_money.pair_manager.class', Tbbc\MoneyBundle\Pair\PairManager::class);
    $parameters->set('tbbc_money.pair_manager_interface.class', Tbbc\MoneyBundle\Pair\PairManagerInterface::class);
    $parameters->set('tbbc_money.money_manager.class', Tbbc\MoneyBundle\Money\MoneyManager::class);
    $parameters->set('tbbc_money.pair_history_manager.class', Tbbc\MoneyBundle\PairHistory\PairHistoryManager::class);
    $parameters->set('tbbc_money.pair_history_manager_interface.class', Tbbc\MoneyBundle\PairHistory\PairHistoryManagerInterface::class);
    $parameters->set('tbbc_money.pair.csv_storage.class', Tbbc\MoneyBundle\Pair\Storage\CsvStorage::class);
    $parameters->set('tbbc_money.pair_manager.ratio_file_name', '%kernel.project_dir%/../data/tbbc_money/ratio_file_name.csv');
    $parameters->set('tbbc_money.ratio_provider.ecb.class', Tbbc\MoneyBundle\Pair\RatioProvider\ECBRatioProvider::class);
    $parameters->set('tbbc_money.formatter.money_formatter.class', Tbbc\MoneyBundle\Formatter\MoneyFormatter::class);
    $parameters->set('tbbc_money.command.ratio_fetch.class', Tbbc\MoneyBundle\Command\RatioFetchCommand::class);
    $parameters->set('tbbc_money.command.ratio_list.class', Tbbc\MoneyBundle\Command\RatioListCommand::class);
    $parameters->set('tbbc_money.command.ratio_save.class', Tbbc\MoneyBundle\Command\RatioSaveCommand::class);

    // === Services ===
    $services->set('tbbc_money.pair_manager', '%tbbc_money.pair_manager.class%')
        ->public()
        ->args([
            new Reference('tbbc_money.pair.csv_storage'),
            '%tbbc_money.currencies%',
            '%tbbc_money.reference_currency%',
            new Reference('event_dispatcher'),
        ]);

    $services->alias('%tbbc_money.pair_manager_interface.class%', 'tbbc_money.pair_manager')->private();

    $services->set('tbbc_money.pair_history_manager', '%tbbc_money.pair_history_manager.class%')
        ->public()
        ->args([
            new Reference(EntityManagerInterface::class),
            '%tbbc_money.reference_currency%',
        ]);

    $services->alias('%tbbc_money.pair_history_manager_interface.class%', 'tbbc_money.pair_history_manager')->private();

    $services->set('tbbc_money.money_manager', '%tbbc_money.money_manager.class%')
        ->public()
        ->args([
            '%tbbc_money.reference_currency%',
            '%tbbc_money.decimals%',
        ]);

    $services->alias('%tbbc_money.money_manager.class%', 'tbbc_money.money_manager')->private();

    // Storage
    $services->set('tbbc_money.pair.csv_storage', '%tbbc_money.pair.csv_storage.class%')
        ->public()
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
        ->public()
        ->args([
            '%tbbc_money.decimals%',
        ]);

    $services->alias('%tbbc_money.formatter.money_formatter.class%', 'tbbc_money.formatter.money_formatter')->private();

    // Commands
    $services->set('tbbc_money.command.ratio_fetch', '%tbbc_money.command.ratio_fetch.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', ['command' => 'tbbc:money:ratio-fetch']);

    $services->set('tbbc_money.command.ratio_list', '%tbbc_money.command.ratio_list.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', ['command' => 'tbbc:money:ratio-list']);

    $services->set('tbbc_money.command.ratio_save', '%tbbc_money.command.ratio_save.class%')
        ->args([new Reference('tbbc_money.pair_manager')])
        ->tag('console.command', ['command' => 'tbbc:money:ratio-save']);
};
