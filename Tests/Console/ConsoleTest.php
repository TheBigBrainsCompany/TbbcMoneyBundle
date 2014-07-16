<?php
namespace Tbbc\MoneyBundle\Tests\Console;

use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Tests\TestUtil\CommandTestCase;

/**
 * @group functionnal
 */
class ConsoleTest
    extends CommandTestCase
{
    /** @var  \Symfony\Bundle\FrameworkBundle\Client */
    private $client;
    public function setUp()
    {
        parent::setUp();
        /** @var \Symfony\Bundle\FrameworkBundle\Client client */
        $this->client = static::createClient();

        $this->runCommand($this->client,'doctrine:database:create');
        $this->runCommand($this->client,'doctrine:schema:update --force');
    }

    public function testRunSaveRatio()
    {
        $client = $this->client;


        $output = $this->runCommand($client, "tbbc:money:ratio-save USD 1.265");

        /** @var PairManagerInterface $pairManager */
        $pairManager = $client->getContainer()->get("tbbc_money.pair_manager");
        $this->assertEquals(1.265, $pairManager->getRelativeRatio("EUR", "USD"));
    }
    public function testRunRatioList()
    {
        $client = $this->client;
        $output = $this->runCommand($client, "tbbc:money:ratio-save USD 1.265");
        $output = $this->runCommand($client, "tbbc:money:ratio-save CAD 1.1");

        $output = $this->runCommand($client, "tbbc:money:ratio-list");

        $this->assertEquals("Ratio list\nEUR;1\nUSD;1.265\nCAD;1.1\n\n", $output);
    }

    public function testRunRatioFetch()
    {
        $client = $this->client;
        $output = $this->runCommand($client, "tbbc:money:ratio-fetch");
        $this->assertNotContains("ERR", $output);

        $output = $this->runCommand($client, "tbbc:money:ratio-list");
        $res = file_get_contents("http://rate-exchange.appspot.com/currency?from=EUR&to=USD");
        $res = json_decode($res, true);
        $ratioUsd = $res["rate"];
        $res = file_get_contents("http://rate-exchange.appspot.com/currency?from=EUR&to=CAD");
        $res = json_decode($res, true);
        $ratioCad = $res["rate"];
        $this->assertEquals("Ratio list\nEUR;1\nUSD;$ratioUsd\nCAD;$ratioCad\n\n", $output);
    }
}