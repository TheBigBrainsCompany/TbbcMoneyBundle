<?php
namespace Tbbc\MoneyBundle\Tests\Console;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Tests\TestUtil\CommandTestCase;

/**
 * @group functionnal
 */
class ConsoleTest
    extends CommandTestCase
{
    /** @var  \Symfony\Bundle\FrameworkBundle\KernelBrowser */
    private $client;
    public function setUp(): void
    {
        parent::setUp();
        /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser client */
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

        $this->assertMatchesRegularExpression("/^Ratio list\nEUR;1\nUSD;\d.\d+\nCAD;\d.\d+\n\n$/", $output);
    }
}
