<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Twig\Extension;

use Locale;
use Money\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Twig\Extension\CurrencyExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TemplateWrapper;

class CurrencyExtensionTest extends TestCase
{
    private CurrencyExtension $extension;

    protected array $variables;

    public function setUp(): void
    {
        Locale::setDefault('fr_FR');
        $this->extension = new CurrencyExtension(new MoneyFormatter(2));
        $this->variables = [
            'currency' => new Currency('EUR'),
        ];
    }

    public function testName(): void
    {
        self::assertSame('tbbc_money_currency_extension', $this->extension->getName());
    }

    #[DataProvider('getCurrencyTests')]
    public function testCurrency($template, $expected): void
    {
        $this->assertSame($expected, $this->getTemplate($template)->render($this->variables));
    }

    public static function getCurrencyTests(): array
    {
        return [
            ['{{ currency|currency_name }}', 'EUR'],
            ['{{ currency|currency_symbol(".", ",") }}', '€'],
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
