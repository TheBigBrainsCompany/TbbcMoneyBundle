<?php
namespace Tbbc\MoneyBundle\Pair;

use Money\Converter;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\StorageInterface;
use Tbbc\MoneyBundle\TbbcMoneyEvents;

/**
 * Class PairManager
 * @package Tbbc\MoneyBundle\Pair
 * @author Philippe Le Van.
 */
class PairManager implements PairManagerInterface, Exchange
{
    /** @var  StorageInterface */
    protected $storage;

    /** @var  array */
    protected $currencyCodeList;

    /** @var  string */
    protected $referenceCurrencyCode;

    /** @var  RatioProviderInterface */
    protected $ratioProvider;

    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    /**
     * @var Currencies
     */
    protected $currencies;

    /**
     * PairManager constructor.
     *
     * @param StorageInterface         $storage
     * @param array                    $currencyCodeList
     * @param string                   $referenceCurrencyCode
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        StorageInterface $storage,
        $currencyCodeList,
        $referenceCurrencyCode,
        EventDispatcherInterface $dispatcher
    ) {
        $this->storage = $storage;
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
        $this->dispatcher = $dispatcher;
        $this->currencies = new ISOCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Money $amount, $currencyCode)
    {
        $converter = new Converter($this->currencies, $this);

        return $converter->convert($amount, new Currency($currencyCode));
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        $ratio = $this->getRelativeRatio($baseCurrency->getCode(), $counterCurrency->getCode());

        return new CurrencyPair($baseCurrency, $counterCurrency, $ratio);
    }

    /**
     * {@inheritdoc}
     */
    public function saveRatio($currencyCode, $ratio)
    {
        $currency = new Currency($currencyCode);
        // end of hack
        $ratio = floatval($ratio);
        if ($ratio <= 0) {
            throw new MoneyException("ratio has to be strictly positive");
        }
        $ratioList = $this->storage->loadRatioList(true);
        $ratioList[$currency->getCode()] = $ratio;
        $ratioList[$this->getReferenceCurrencyCode()] = (float) 1;
        $this->storage->saveRatioList($ratioList);

        $savedAt = new \DateTime();
        $event = new SaveRatioEvent(
            $this->getReferenceCurrencyCode(),
            $currencyCode,
            $ratio,
            $savedAt
        );
        $this->dispatcher->dispatch(TbbcMoneyEvents::AFTER_RATIO_SAVE, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativeRatio($referenceCurrencyCode, $currencyCode)
    {
        $currency = new Currency($currencyCode);
        $referenceCurrency = new Currency($referenceCurrencyCode);
        if ($currencyCode === $referenceCurrencyCode) {
            return (float) 1;
        }
        $ratioList = $this->storage->loadRatioList();
        if (!array_key_exists($currency->getCode(), $ratioList)) {
            throw new MoneyException("unknown ratio for currency $currencyCode");
        }
        if (!array_key_exists($referenceCurrency->getCode(), $ratioList)) {
            throw new MoneyException("unknown ratio for currency $referenceCurrencyCode");
        }

        return $ratioList[$currency->getCode()] / $ratioList[$referenceCurrency->getCode()];
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCodeList()
    {
        return $this->currencyCodeList;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCurrencyCode()
    {
        return $this->referenceCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatioList()
    {
        return $this->storage->loadRatioList();
    }

    /**
     * {@inheritdoc}
     */
    public function setRatioProvider(RatioProviderInterface $ratioProvider)
    {
        $this->ratioProvider = $ratioProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRatioListFromRatioProvider()
    {
        if (!$this->ratioProvider) {
            throw new MoneyException("no ratio provider defined");
        }
        foreach ($this->getCurrencyCodeList() as $currencyCode) {
            if ($currencyCode != $this->getReferenceCurrencyCode()) {
                $ratio = $this->ratioProvider->fetchRatio($this->getReferenceCurrencyCode(), $currencyCode);
                $this->saveRatio($currencyCode, $ratio);
            }
        }
    }
}
