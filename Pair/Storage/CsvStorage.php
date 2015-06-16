<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/07/13
 */

namespace Tbbc\MoneyBundle\Pair\Storage;


use Money\Currency;
use Money\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\StorageInterface;

class CsvStorage
    implements StorageInterface
{
    /** @var  string */
    protected $ratioFileName;

    /** @var array  */
    protected $ratioList = array();

    /** @var  string */
    protected $referenceCurrencyCode;

    public function __construct(
        $ratioFileName,
        $referenceCurrencyCode
    )
    {
        $this->ratioFileName = $ratioFileName;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * @inheritdoc
     */
    public function loadRatioList($force = false)
    {
        if ( ($force === false) && (count($this->ratioList) > 0) ) {
            return $this->ratioList;
        }
        // if filename doesn't exist, init with only reference currency code
        if (!is_file($this->ratioFileName)) {
            $currencyCodePair = $this->referenceCurrencyCode . '/' . $this->referenceCurrencyCode;
            $this->ratioList = array($currencyCodePair => (float) 1);
            $this->saveRatioList($this->ratioList);
            return $this->ratioList;
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
            list($currencyCodePair, $ratio) = $data;


            // TODO: validate that currency exist in currency code list.
            // It should be util from mathiasverraes/money library, not a object creation

            // validate value
            $ratio = floatval($ratio);
            if (!$ratio) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio is not a float or is null");
            }
            if ($ratio <= 0) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio has to be positive");
            }

            // validate if currency is twice in the file
            if (array_key_exists($currencyCodePair, $this->ratioList)) {
                throw new MoneyException("error in ratioFileName $this->ratioFileName on line $row, ratio already exists for currency $currencyCodePair");
            }

            $this->ratioList[$currencyCodePair] = $ratio;
            $row++;
        }
        fclose($handle);
        return $this->ratioList;
    }

    public function saveRatioList($ratioList)
    {
        $dirName = dirname($this->ratioFileName);
        if (!is_dir($dirName)) {
            mkdir($dirName,0777, true);
        }
        if (($handle = fopen($this->ratioFileName, "w")) === FALSE) {
            throw new MoneyException("can't open $this->ratioFileName for writing");
        }
        foreach ($ratioList as $currencyCodePair => $ratio) {
            fputcsv($handle, array($currencyCodePair, $ratio), ';');
        }
        fclose($handle);
        $this->ratioList = $ratioList;
    }


}