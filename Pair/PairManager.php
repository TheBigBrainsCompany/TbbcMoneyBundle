<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Pair;

use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\TbbcMoneyEvents;
use Tbbc\MoneyBundle\Utils\CurrencyUtils;

class PairManager
    implements PairManagerInterface
{
    /** @var  StorageInterface */
    protected $storage;

    /** @var  array */
    protected $currencyCodeList;

    /** @var  Currency */
    protected $referenceCurrency;

    /** @var  RatioProviderInterface */
    protected $ratioProvider;

    /** @var EventDispatcherInterface  */
    protected $dispatcher;

    public function __construct(
        StorageInterface $storage,
        $currencyCodeList,
        $referenceCurrencyCode,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->storage = $storage;
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrency = new Currency($referenceCurrencyCode);
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function convert(Money $amount, $currencyTo)
    {
        if (CurrencyUtils::isCurrency($currencyTo)) {
            $targetCurrency = $currencyTo;
        } elseif (CurrencyUtils::isInCurrencyCodeFormat($currencyTo)) {
            $targetCurrency = new Currency($currencyTo);
        } else {
            throw new MoneyException("Can't create currencyTo");
        }
        $ratio = $this->getRelativeRatio($amount->getCurrency(), $targetCurrency);
        $pair = new CurrencyPair($amount->getCurrency(), $targetCurrency, $ratio);
        return $pair->convert($amount);
    }

    /**
     * @inheritdoc
     */
    public function saveRatio($currencyTo, $ratio, $currencyFrom = null)
    {
        $toCurrency = CurrencyUtils::isCurrency($currencyTo) ? $currencyTo : new Currency($currencyTo);
        if (null === $currencyFrom) {
            $fromCurrency = $this->getReferenceCurrency();
            $isFromReference = true;
        } else {
            $fromCurrency = CurrencyUtils::isCurrency($currencyFrom) ? $currencyFrom : new Currency($currencyFrom);
            $isFromReference = false;
        }

        $ratio = floatval($ratio);
        if ($fromCurrency->equals($toCurrency)) {
            $ratio = 1.;
        } if ($ratio <= 0) {
            throw new MoneyException("ratio has to be strictly positive");
        }
        $ratioList = $this->storage->loadRatioList(true);
        $ratioKey = $isFromReference ? $toCurrency->getName() : $fromCurrency->getName() . '/' . $toCurrency->getName();
        $ratioList[$ratioKey] = $ratio;
        $this->storage->saveRatioList($ratioList);

        $savedAt = new \DateTime();
        $event = new SaveRatioEvent(
            $fromCurrency->getName(),
            $toCurrency->getName(),
            $ratio,
            $savedAt
        );
        $this->dispatcher->dispatch(TbbcMoneyEvents::AFTER_RATIO_SAVE, $event);
    }

    /**
     * @inheritdoc
     */
    public function getRelativeRatio($currencyFrom, $currencyTo, $isStrict = false)
    {
        $currency = CurrencyUtils::isCurrency($currencyFrom) ? $currencyFrom : new Currency($currencyFrom);
        $targetCurrency = CurrencyUtils::isCurrency($currencyTo) ? $currencyTo : new Currency($currencyTo);
        if ($targetCurrency->equals($currency)) {
            return 1.;
        }
        $ratioList = $this->storage->loadRatioList();
        $currencyCodePair = $currency->getName() . '/' . $targetCurrency->getName();
        if (!array_key_exists($currencyCodePair, $ratioList)) {
            if (!array_key_exists($targetCurrency->getName(), $ratioList)
                or !array_key_exists($currency->getName(), $ratioList)
                or $isStrict
            ) {
                throw new MoneyException('unknown ratio for currency ' . $currencyFrom . ' to ' . $currencyTo);
            }
            return $ratioList[$targetCurrency->getName()] / $ratioList[$currency->getName()];
        }
        return $ratioList[$currencyCodePair];
    }

    /**
     * @inheritdoc
     */
    public function getCurrencyCodeList()
    {
        return $this->currencyCodeList;
    }

    /**
     * @inheritdoc
     */
    public function getReferenceCurrencyCode()
    {
        return $this->referenceCurrency->getName();
    }

    /**
     * @inheritdoc
     */
    public function getReferenceCurrency()
    {
        return $this->referenceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function getRatioList()
    {
        return $this->storage->loadRatioList();
    }

    /**
     * @inheritdoc
     */
    public function setRatioProvider(RatioProviderInterface $ratioProvider)
    {
        $this->ratioProvider = $ratioProvider;
    }

    /**
     * @inheritdoc
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
