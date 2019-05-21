<?php
namespace Tbbc\MoneyBundle\Twig\Extension;

use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Philippe Le Van <philippe.levan@kitpages.fr>
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class MoneyExtension extends AbstractExtension
{
    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;

    /**
     * @var PairManagerInterface
     */
    protected $pairManager;

    /**
     * Constructor
     *
     * @param MoneyFormatter       $moneyFormatter
     * @param PairManagerInterface $pairManager
     */
    public function __construct(MoneyFormatter $moneyFormatter, PairManagerInterface $pairManager)
    {
        $this->moneyFormatter = $moneyFormatter;
        $this->pairManager = $pairManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('money_localized_format', array($this->moneyFormatter, 'localizedFormatMoney')),
            new TwigFilter('money_format', array($this->moneyFormatter, 'formatMoney')),
            new TwigFilter('money_format_amount', array($this->moneyFormatter, 'formatAmount')),
            new TwigFilter('money_format_currency', array($this->moneyFormatter, 'formatCurrency')),
            new TwigFilter('money_as_float', array($this->moneyFormatter, 'asFloat')),
            new TwigFilter('money_get_currency', array($this->moneyFormatter, 'getCurrency')),
            new TwigFilter('money_convert', array($this, 'convert')),
        );
    }

    /**
     * Converts the given Money object into another
     * currency and returns a new Money object
     *
     * @param Money  $money
     * @param string $currencyCode
     * @return Money
     */
    public function convert(Money $money, $currencyCode)
    {
        return $this->pairManager->convert($money, $currencyCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tbbc_money_extension';
    }
}
