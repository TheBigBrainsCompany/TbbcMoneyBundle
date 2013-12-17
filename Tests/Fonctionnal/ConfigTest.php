<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Config;

use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Twig\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\MoneyExtension;
use Tbbc\MoneyBundle\Type\MoneyType;
use Doctrine\DBAL\Types\Type;


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
        \Locale::setDefault('en');
        $client = self::createClient();
        /** @var PairManagerInterface $pairManager */
        $pairManager = $client->getContainer()->get("tbbc_money.pair_manager");
        $pairManager->saveRatio("USD", 1.25);
        /** @var MoneyExtension $moneyExtension */
        $moneyExtension = $client->getContainer()->get("tbbc_money.twig.money");
        $eur = Money::EUR(100);
        $usd = $moneyExtension->convert($eur, "USD");
        $this->assertEquals(Money::USD(125), $usd);
    }

    public function testCurrencyTwigExtension()
    {
        \Locale::setDefault('en');
        $client = self::createClient();
        /** @var CurrencyExtension $currencyExtension */
        $currencyExtension = $client->getContainer()->get("tbbc_money.twig.currency");
    }
    
    public function testDoctrineMoneyTypeAvailable()
    {
        $client = self::createClient();
        
        $this->assertTrue(Type::hasType(MoneyType::NAME));
        
        $em = $client->getContainer()->get('doctrine')->getManager('default');
        $this->assertEquals(MoneyType::NAME, $em->getConnection()->getDatabasePlatform()->getDoctrineTypeMapping(MoneyType::NAME));
    }
}