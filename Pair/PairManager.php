<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Pair;

use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Money\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;

class PairManager
    implements PairManagerInterface
{
    /** @var  string */
    protected $ratioFileName;

    /** @var array  */
    protected $ratioList = array();

    /** @var  array */
    protected $currencyCodeList;

    /** @var  string */
    protected $referenceCurrencyCode;

    public function __construct(
        $ratioFileName,
        $currencyCodeList,
        $referenceCurrencyCode
    )
    {
        $this->ratioFileName = $ratioFileName;
        $this->currencyCodeList = $currencyCodeList;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
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
        $ratio = floatval($ratio);
        if ($ratio <= 0) {
            throw new MoneyException("ratio has to be strictly positive");
        }
        $this->loadCurrencyRatioList();
        $this->ratioList[$currencyCode] = $ratio;
        $this->ratioList[$this->getReferenceCurrencyCode()] = (float) 1;
        $this->saveCurrencyRatioList($this->ratioList);
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
        $this->loadCurrencyRatioList();
        if (!array_key_exists($currencyCode, $this->ratioList)) {
            throw new MoneyException("unknown ratio for currency $currencyCode");
        }
        if (!array_key_exists($referenceCurrencyCode, $this->ratioList)) {
            throw new MoneyException("unknown ratio for currency $referenceCurrencyCode");
        }
        return $this->ratioList[$currencyCode] / $this->ratioList[$referenceCurrencyCode];
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
        return $this->ratioList;
    }

    /**
     * load $this->ratioList
     *
     * @param bool $force
     * @throws \Tbbc\MoneyBundle\MoneyException
     */
    protected function loadCurrencyRatioList($force = false)
    {
        if ( ($force === false) && (count($this->ratioList) > 0) ) {
            return;
        }
        // if filename doesn't exist, init with only reference currency code
        if (!is_file($this->ratioFileName)) {
            $this->saveCurrencyRatioList(array(
                $this->getReferenceCurrencyCode() => (float) 1
            ));
        }
        // read ratio file
        if (($handle = fopen($this->ratioFileName, "r")) === FALSE) {
            throw new MoneyException("ratioFileName $this->ratioFileName not initialized");
        }
        $row = 1;
        $this->ratioList = array();
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // extract data from CSV line
            if (count($data) != 2) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, invalid argument count");
            }
            list($currencyCode, $ratio) = $data;


            // validate that currency exist in currency code list
            try {
                $currency = new Currency($currencyCode);
            } catch (UnknownCurrencyException $e) {
//                echo file_get_contents($this->ratioFileName);
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, unknown currency $currencyCode");
            }

            // validate value
            $ratio = floatval($ratio);
            if (!$ratio) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio is not a float or is null");
            }
            if ($ratio <= 0) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio has to be positive");
            }

            // validate if currency is twice in the file
            if (array_key_exists($currencyCode, $this->ratioList)) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio already exists for currency $currencyCode");
            }

            $this->ratioList[$currencyCode] = $ratio;
            $row++;
        }
        fclose($handle);
    }

    protected function saveCurrencyRatioList($ratioList)
    {
        $dirName = dirname($this->ratioFileName);
        if (!is_dir($dirName)) {
            mkdir($dirName,0777, true);
        }
        if (($handle = fopen($this->ratioFileName, "w")) === FALSE) {
            throw new MoneyException("can't open $this->ratioFileName for writing");
        }
        foreach ($ratioList as $currencyCode => $ratio) {
            fputcsv($handle, array($currencyCode, $ratio), ';');
        }
        fclose($handle);
    }

}