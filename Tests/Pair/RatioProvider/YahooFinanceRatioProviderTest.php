<?php

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Money\Currency;
use Tbbc\MoneyBundle\Pair\RatioProvider\YahooFinanceRatioProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 * @group  manager
 */
class YahooFinanceRatioProviderTest extends TestCase
{
    public function testRatioFetchingEUR_USD()
    {
        $providerMock = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\RatioProvider\YahooFinanceRatioProvider')
            ->setMethods(array('getEndpoint', 'executeQuery'))
            ->getMock();

        $providerMock->expects($this->any())
            ->method('getEndpoint')
            ->with(new Currency('EUR'), new Currency('USD'))
            ->willReturn($this->getUrl_EUR_USD());

        $providerMock->expects($this->any())
            ->method('executeQuery')
            ->with($this->getUrl_EUR_USD())
            ->willReturn($this->getOutput_EUR_USD());

        $this->assertSame(1.0856, $providerMock->fetchRatio('EUR', 'USD'));
    }

    public function testRatioFetchingGBP_EUR()
    {
        $providerMock = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\RatioProvider\YahooFinanceRatioProvider')
            ->setMethods(array('getEndpoint', 'executeQuery'))
            ->getMock();

        $providerMock->expects($this->any())
            ->method('getEndpoint')
            ->with(new Currency('GBP'), new Currency('EUR'))
            ->willReturn($this->getUrl_GBP_EUR());

        $providerMock->expects($this->any())
            ->method('executeQuery')
            ->with($this->getUrl_GBP_EUR())
            ->willReturn($this->getOutput_GBP_EUR());

        $this->assertSame(1.1181, $providerMock->fetchRatio('GBP', 'EUR'));
    }

    public function testExceptionForUnknownCurrency()
    {
        $ratioProvider = new YahooFinanceRatioProvider();

        $this->setExpectedException('Tbbc\MoneyBundle\MoneyException');
        $ratioProvider->fetchRatio('ZZZ', 'USD');
    }

    /**
     * @return string
     */
    private function getUrl_EUR_USD()
    {
        return 'https://query.yahooapis.com/v1/public/yql?q=select+%2A+from+yahoo.finance.xchange+where+pair+in+%28%22EURUSD%22%29&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';
    }

    /**
     * @return string
     */
    private function getOutput_EUR_USD()
    {
        return '{"query":{"count":1,"created":"2016-10-25T17:07:37Z","lang":"en-US","results":{"rate":{"id":"EURUSD","Name":"EUR/USD","Rate":"1.0856","Date":"10/25/2016","Time":"3:55pm","Ask":"1.0857","Bid":"1.0856"}}}}';
    }

    /**
     * @return string
     */
    private function getUrl_GBP_EUR()
    {
        return 'https://query.yahooapis.com/v1/public/yql?q=select+%2A+from+yahoo.finance.xchange+where+pair+in+%28%22GBPEUR%22%29&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';
    }

    /**
     * @return string
     */
    private function getOutput_GBP_EUR()
    {
        return '{"query":{"count":1,"created":"2016-10-25T17:07:39Z","lang":"en-US","results":{"rate":{"id":"GBPEUR","Name":"GBP/EUR","Rate":"1.1181","Date":"10/25/2016","Time":"5:21pm","Ask":"1.1182","Bid":"1.1181"}}}}';
    }
}
