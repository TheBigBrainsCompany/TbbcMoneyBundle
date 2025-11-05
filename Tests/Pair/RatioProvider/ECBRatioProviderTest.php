<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\RatioProvider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProvider\ECBRatioProvider;

class ECBRatioProviderTest extends TestCase
{
    private MockHttpClient $client;
    private ECBRatioProvider $ratio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new MockHttpClient();
        $this->ratio = new ECBRatioProvider($this->client);
    }

    public function testFetchRatio(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/ecbxml/correct.xml')),
        ]);

        $ratio = $this->ratio->fetchRatio('EUR', 'USD');
        self::assertSame(1.1273, $ratio);
    }

    public function testGetCachedData(): void
    {
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/ecbxml/correct.xml')),
        ]);

        $ratio = $this->ratio->fetchRatio('EUR', 'USD');
        self::assertSame(1.1273, $ratio);

        $ratio = $this->ratio->fetchRatio('EUR', 'USD');
        self::assertSame(1.1273, $ratio);
    }

    public function testNotCorrectReferenceCode(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('The reference currency code for ECB provider must be EUR, got: "USD"');
        $this->ratio->fetchRatio('USD', 'EUR');
    }

    public function testUnknownCurrency(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('The currency code is an empty string');
        $this->ratio->fetchRatio('EUR', '');
    }

    public function test404(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('The request to https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml failed with status code 404');
        $this->client->setResponseFactory([
            new MockResponse('', ['http_code' => 404]),
        ]);
        $this->ratio->fetchRatio('EUR', 'USD');
    }

    public function testUnknownXML(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('Could not fetch XML from ECB');
        $this->client->setResponseFactory([
            new MockResponse(''),
        ]);
        $this->ratio->fetchRatio('EUR', 'USD');
    }

    public function testCurrencyCodeNotExists(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('The currency code TTT does not exist in the ECB feed');
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/ecbxml/correct.xml')),
        ]);
        $this->ratio->fetchRatio('EUR', 'TTT');
    }

    public function testFailedToParseXml(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('Failed to parse XML from ECB');
        $this->client->setResponseFactory([
            new MockResponse(file_get_contents(__DIR__.'/ecbxml/incorrect.txt')),
        ]);
        $this->ratio->fetchRatio('EUR', 'USD');
    }
}
