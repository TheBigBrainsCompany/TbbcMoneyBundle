<?php
namespace Tbbc\MoneyBundle\Tests\Fonctionnal;

use Locale;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Tbbc\MoneyBundle\Money\MoneyManager;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @group functionnal
 */
class ConfigTest extends WebTestCase
{
    private KernelBrowser $client;
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->runCommand('doctrine:database:drop --force');
        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update --force');
    }

    protected function runCommand($command): int
    {
        $command = sprintf('%s --quiet', $command);

        $application = new Application($this->client->getKernel());
        $application->setAutoExit(false);

        return $application->run(new StringInput($command));
    }

    public function testConfigParsing()
    {
        $currencies = $this->client->getContainer()->getParameter('tbbc_money.currencies');
        $this->assertEquals(array("USD", "EUR", 'CAD'), $currencies);

        $referenceCurrency = $this->client->getContainer()->getParameter('tbbc_money.reference_currency');
        $this->assertEquals("EUR", $referenceCurrency);
    }

    public function testMoneyTwigExtension()
    {
        Locale::setDefault('en');
        /** @var PairManagerInterface $pairManager */
        $pairManager = $this->client->getContainer()->get("tbbc_money.pair_manager");
        $pairManager->saveRatio("USD", 1.25);
        /** @var MoneyExtension $moneyExtension */
        $moneyExtension = $this->client->getContainer()->get("tbbc_money.twig.money");
        $eur = Money::EUR(100);
        $usd = $moneyExtension->convert($eur, "USD");
        $this->assertEquals(Money::USD(125), $usd);
    }

    public function testMoneyManager()
    {
        /** @var MoneyManager $moneyManager */
        $moneyManager = $this->client->getContainer()->get("tbbc_money.money_manager");
        $money = $moneyManager->createMoneyFromFloat('2.5', 'USD');
        $this->assertEquals("USD", $money->getCurrency()->getCode());
        $this->assertEquals(2500, $money->getAmount()); // note : 3 decimals in config for theses tests
    }

    public function testHistoryRatio()
    {
        Locale::setDefault('en');
        /** @var PairManagerInterface $pairManager */
        $pairManager = $this->client->getContainer()->get("tbbc_money.pair_manager");
        $pairManager->saveRatio("USD", 1.25);
        sleep(1);
        $between = new \DateTime();
        sleep(1);
        $pairManager->saveRatio("USD", 1.50);
        $now = new \DateTime();
        $before = clone($now);
        $before->sub(new \DateInterval('P1D'));
        $pairHistoryManager = $this->client->getContainer()->get("tbbc_money.pair_history_manager");
        $ratio = $pairHistoryManager->getRatioAtDate('USD', $between);
        $this->assertEquals(1.25, $ratio);
        $ratio = $pairHistoryManager->getRatioAtDate('USD', $now);
        $this->assertEquals(1.5, $ratio);
        $ratio = $pairHistoryManager->getRatioAtDate('USD', $before);
        $this->assertEquals(null, $ratio);


        $em = $this->client->getContainer()->get("doctrine.orm.entity_manager");
        $repo = $em->getRepository('\Tbbc\MoneyBundle\Entity\RatioHistory');
        $list = $repo->findAll();
        $this->assertCount(2, $list);

    }

    public function testHistoryOfFetchedRatio()
    {
        $this->runCommand('tbbc:money:ratio-fetch');
        $em = $this->client->getContainer()->get("doctrine.orm.entity_manager");
        $repo = $em->getRepository('\Tbbc\MoneyBundle\Entity\RatioHistory');
        $list = $repo->findAll();

        $this->assertCount(2, $list);
    }

    public function testCurrencyTwigExtension()
    {
        Locale::setDefault('en');
        /** @var CurrencyExtension $currencyExtension */
        $currencyExtension = $this->client->getContainer()->get("tbbc_money.twig.currency");

        $this->assertInstanceOf(CurrencyExtension::class, $currencyExtension);
    }
}