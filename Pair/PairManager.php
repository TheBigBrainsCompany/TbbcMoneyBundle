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
use Tbbc\MoneyBundle\Pair\StorageInterface;
use Tbbc\MoneyBundle\TbbcMoneyEvents;

class PairManager
    implements PairManagerInterface
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

    public function __construct(
        StorageInterface $storage,
        $currencyCodeList,
        $referenceCurrencyCode,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->storage = $storage;
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function convert(Money $amount, $currencyCode)
    {
        $ratio = $this->getRelativeRatio($amount->getCurrency()->getName(), $currencyCode);
        $pair = new CurrencyPair($amount->getCurrency(), new Currency($currencyCode), $ratio);
        return $pair->convert($amount);
    }

    /**
     * @inheritdoc
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
        $ratioList[$currency->getName()] = $ratio;
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
     * @inheritdoc
     */
    public function getRelativeRatio($referenceCurrencyCode, $currencyCode)
    {
        $currency = new Currency($currencyCode);
        $referenceCurrency = new Currency($referenceCurrencyCode);
        if ($currencyCode === $referenceCurrencyCode) {
            return (float) 1;
        }
        $ratioList = $this->storage->loadRatioList();
        if (!array_key_exists($currency->getName(), $ratioList)) {
            throw new MoneyException("unknown ratio for currency $currencyCode");
        }
        if (!array_key_exists($referenceCurrency->getName(), $ratioList)) {
            throw new MoneyException("unknown ratio for currency $referenceCurrencyCode");
        }
        return $ratioList[$currency->getName()] / $ratioList[$referenceCurrency->getName()];
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
        return $this->referenceCurrencyCode;
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
