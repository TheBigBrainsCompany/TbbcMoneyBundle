<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('tbbc_money_currency_list', '/currency-list')
        ->controller('Tbbc\MoneyBundle\Controller\CurrencyController::listAction');
};
