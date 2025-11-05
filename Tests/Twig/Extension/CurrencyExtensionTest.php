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

final class CurrencyExtensionTest extends TestCase
{
    private CurrencyExtension $extension;

    private array $variables;

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
        $this->assertSame('tbbc_money_currency_extension', $this->extension->getName());
    }

    #[DataProvider('getCurrencyTests')]
    public function testCurrency(string $template, string $expected): void
    {
        $this->assertSame($expected, $this->getTemplate($template)->render($this->variables));
    }

    public static function getCurrencyTests(): \Iterator
    {
        yield ['{{ currency|currency_name }}', 'EUR'];
        yield ['{{ currency|currency_symbol(".", ",") }}', '€'];
    }

    private function getTemplate(string $template): TemplateWrapper
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
