<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Twig\Extension;

use Locale;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class MoneyExtensionTest extends TestCase
{
    private MoneyExtension $extension;

    protected array $variables;
    private MockObject|PairManager $pairManager;

    public function setUp(): void
    {
        Locale::setDefault('fr_FR');
        $this->pairManager = $this->createMock(PairManager::class);
        $this->extension = new MoneyExtension(new MoneyFormatter(2), $this->pairManager);
        $this->variables = [
            'price' => new Money(123456789, new Currency('EUR')),
        ];
    }

    public function testConvert(): void
    {
        $money = new Money(10000, new Currency('EUR'));
        $retMoney = new Money(5000, new Currency('USD'));
        $this->pairManager
            ->expects($this->once())
            ->method('convert')
            ->with($money, 'USD')
            ->willReturn($retMoney);

        $this->assertSame(
            $retMoney,
            $this->extension->convert($money, 'USD')
        );
    }

    public function testName(): void
    {
        self::assertSame('tbbc_money_extension', $this->extension->getName());
    }

    #[DataProvider('getMoneyTests')]
    public function testMoney($template, $expected): void
    {
        $this->assertSame($expected, $this->getTemplate($template)->render($this->variables));
    }

    public static function getMoneyTests(): array
    {
        return [
            ['{{ price|money_localized_format }}', "1\u{202f}234\u{202f}567,89\u{a0}€"],
            ['{{ price|money_localized_format("en_US") }}', '€1,234,567.89'],
            ['{{ price|money_format }}', '1 234 567,89 €'],
            ['{{ price|money_format(".", ",") }}', '1,234,567.89 €'],
            ['{{ price|money_format_amount }}', '1 234 567,89'],
            ['{{ price|money_format_amount(".", ",") }}', '1,234,567.89'],
            ['{{ price|money_format_currency }}', '€'],
            ['{{ price|money_as_float }}', '1234567.89'],
        ];
    }

    protected function getTemplate($template): TemplateWrapper
    {
        $loader = new ArrayLoader([
            'index' => $template,
        ]);
        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
        $twig->addExtension($this->extension);

        /* @noinspection PhpTemplateMissingInspection */
        return $twig->load('index');
    }
}
