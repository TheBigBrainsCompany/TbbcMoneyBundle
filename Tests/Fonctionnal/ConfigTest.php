<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Config;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;


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

}