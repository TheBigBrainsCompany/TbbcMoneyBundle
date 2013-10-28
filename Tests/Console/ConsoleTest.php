<?php
namespace Tbbc\MoneyBundle\Tests\Console;

use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Tests\TestUtil\CommandTestCase;

class ConsoleTest
    extends CommandTestCase
{
    public function testRunSaveRatio()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "tbbc:money:ratio-save USD 1.265");

        /** @var PairManagerInterface $pairManager */
        $pairManager = $client->getContainer()->get("tbbc_money.pair_manager");
        $this->assertEquals(1.265, $pairManager->getRelativeRatio("EUR", "USD"));
    }
    public function testRunRatioList()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, "tbbc:money:ratio-save USD 1.265");

        $output = $this->runCommand($client, "tbbc:money:ratio-list");

        $this->assertEquals("Ratio list\nEUR;1\nUSD;1.265\n\n", $output);
    }

    public function testRunRatioFetch()
    {
        $client = self::createClient();
        $output = $this->runCommand($client, "tbbc:money:ratio-fetch");
        $this->assertNotContains("ERR", $output);

        $output = $this->runCommand($client, "tbbc:money:ratio-list");
        $res = file_get_contents("http://rate-exchange.appspot.com/currency?from=EUR&to=USD");
        $res = json_decode($res, true);
        $ratio = $res["rate"];
        $this->assertEquals("Ratio list\nEUR;1\nUSD;$ratio\n\n", $output);
    }
}