<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair;

use DateTime;
use Money\Converter;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exchange;
use Money\Money;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\TbbcMoneyEvents;

/**
 * @author Philippe Le Van.
 */
class PairManager implements PairManagerInterface, Exchange
{
    protected ?RatioProviderInterface $ratioProvider = null;
    protected Currencies $currencies;

    public function __construct(
        protected StorageInterface $storage,
        /** @var string[] */
        protected array $currencyCodeList,
        protected string $referenceCurrencyCode,
        protected EventDispatcherInterface $dispatcher
    ) {
        $this->currencies = new ISOCurrencies();
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Money $amount, string $currencyCode): Money
    {
        if ('' === $currencyCode) {
            throw new MoneyException('currency can not be an empty string');
        }

        $converter = new Converter($this->currencies, $this);

        return $converter->convert($amount, new Currency($currencyCode));
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        $ratio = $this->getRelativeRatio($baseCurrency->getCode(), $counterCurrency->getCode());

        return new CurrencyPair($baseCurrency, $counterCurrency, (string) $ratio);
    }

    /**
     * {@inheritdoc}
     */
    public function saveRatio(string $currencyCode, float $ratio): void
    {
        if ('' === $currencyCode) {
            throw new MoneyException('currency can not be an empty string');
        }

        $currency = new Currency($currencyCode);

        if ($ratio <= 0) {
            throw new MoneyException('ratio has to be strictly positive');
        }

        $ratioList = $this->storage->loadRatioList(true);
        $ratioList[$currency->getCode()] = $ratio;
        $ratioList[$this->getReferenceCurrencyCode()] = 1.0;
        $this->storage->saveRatioList($ratioList);

        $savedAt = new DateTime();
        $event = new SaveRatioEvent(
            $this->getReferenceCurrencyCode(),
            $currencyCode,
            $ratio,
            $savedAt
        );

        $this->dispatcher->dispatch($event, TbbcMoneyEvents::AFTER_RATIO_SAVE);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativeRatio(string $referenceCurrencyCode, string $currencyCode): float
    {
        if ('' === $referenceCurrencyCode) {
            throw new MoneyException('reference currency can not be an empty string');
        }

        if ('' === $currencyCode) {
            throw new MoneyException('currency can not be an empty string');
        }

        $currency = new Currency($currencyCode);
        $referenceCurrency = new Currency($referenceCurrencyCode);
        if ($currencyCode === $referenceCurrencyCode) {
            return 1.0;
        }

        $ratioList = $this->storage->loadRatioList();
        if (!array_key_exists($currency->getCode(), $ratioList)) {
            throw new MoneyException('unknown ratio for currency '.$currencyCode);
        }

        if (!array_key_exists($referenceCurrency->getCode(), $ratioList)) {
            throw new MoneyException('unknown ratio for currency '.$referenceCurrencyCode);
        }

        /** @var float $source */
        $source = $ratioList[$currency->getCode()];
        /** @var float $reference */
        $reference = $ratioList[$referenceCurrency->getCode()];

        return $source / $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCodeList(): array
    {
        return $this->currencyCodeList;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceCurrencyCode(): string
    {
        return $this->referenceCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatioList(): array
    {
        return $this->storage->loadRatioList();
    }

    /**
     * {@inheritdoc}
     */
    public function setRatioProvider(RatioProviderInterface $ratioProvider): void
    {
        $this->ratioProvider = $ratioProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRatioListFromRatioProvider(): void
    {
        if (null === $this->ratioProvider) {
            throw new MoneyException('no ratio provider defined');
        }

        foreach ($this->getCurrencyCodeList() as $currencyCode) {
            if ($currencyCode != $this->getReferenceCurrencyCode()) {
                $ratio = $this->ratioProvider->fetchRatio($this->getReferenceCurrencyCode(), $currencyCode);
                $this->saveRatio($currencyCode, $ratio);
            }
        }
    }
}
