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
        $kernel = $client->getKernel();

        $output = $this->runCommand($client, "tbbc:money:save-ratio USD 1.265");

        /** @var PairManagerInterface $pairManager */
        $pairManager = $client->getContainer()->get("tbbc_money.pair_manager");
        $this->assertEquals(1.265, $pairManager->getRelativeRatio("EUR", "USD"));
    }
}