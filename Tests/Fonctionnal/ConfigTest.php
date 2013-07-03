<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Config;

use Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Twig\MoneyExtension;


class ConfigTest
    extends WebTestCase
{
    public function testConfigParsing()
    {
        $client = self::createClient();
        $currencies = $client->getContainer()->getParameter('tbbc_money.currencies');
        $this->assertEquals(array("USD", "EUR"), $currencies);

        $referenceCurrency = $client->getContainer()->getParameter('tbbc_money.reference_currency');
        $this->assertEquals("EUR", $referenceCurrency);
    }

    public function testMoneyTwigExtension()
    {
        $client = self::createClient();
        /** @var PairManagerInterface $pairManager */
        $pairManager = $client->getContainer()->get("tbbc_money.pair_manager");
        $pairManager->saveRatio("USD", 1.25);
        /** @var MoneyExtension $moneyExtension */
        $moneyExtension = $client->getContainer()->get("tbbc_money.twig.money");
        $eur = Money::EUR(100);
        $usd = $moneyExtension->convert($eur, "USD");
        $this->assertEquals(Money::USD(125), $usd);

        $this->assertEquals("1,25 USD", $moneyExtension->format($usd));
        $this->assertEquals(1.25, $moneyExtension->asFloat($usd));
        $this->assertEquals("USD", $moneyExtension->getCurrency($usd));
    }

}