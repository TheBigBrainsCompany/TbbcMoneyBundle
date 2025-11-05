<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\Storage;

use InvalidArgumentException;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\StorageInterface;

/**
 * Class CsvStorage.
 *
 * @author Philippe Le Van.
 */
class CsvStorage implements StorageInterface
{
    /**
     * @psalm-var array<string, null|float>
     */
    protected array $ratioList = [];

    public function __construct(protected string $ratioFileName, protected string $referenceCurrencyCode)
    {
    }

    public function loadRatioList(bool $force = false): array
    {
        if ((false === $force) && ($this->ratioList !== [])) {
            return $this->ratioList;
        }

        // if filename doesn't exist, init with only reference currency code
        if (!is_file($this->ratioFileName)) {
            $this->ratioList = [
                $this->referenceCurrencyCode => 1.0,
            ];
            $this->saveRatioList($this->ratioList);

            return $this->ratioList;
        }

        // read ratio file
        if (($handle = fopen($this->ratioFileName, 'r')) === false) {
            throw new MoneyException('ratioFileName ' . $this->ratioFileName . ' is not initialized');
        }

        $row = 1;
        $this->ratioList = [];

        while (($data = fgetcsv($handle, 1000, ';', '"', '\\')) !== false) {
            // extract data from CSV line
            if (2 !== count($data)) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', invalid argument count');
            }

            [$currencyCode, $ratio] = $data;

            // validate that currency exist in currency code list
            if (null === $currencyCode || '' === $currencyCode) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', currency is an empty string or is null');
            }

            try {
                // hack to throw an exception if currency doesn't exist
                new Currency($currencyCode);
            } catch (UnknownCurrencyException|InvalidArgumentException) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', unknown currency ' . $currencyCode);
            }

            // validate value
            $ratio = floatval($ratio);
            if ($ratio === 0.0) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', ratio is not a float or is null');
            }

            if ($ratio <= 0) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', ratio has to be positive');
            }

            // validate if currency is twice in the file
            if (array_key_exists($currencyCode, $this->ratioList)) {
                throw new MoneyException('error in ratioFileName ' . $this->ratioFileName . ' on line ' . $row . ', ratio already exists for currency ' . $currencyCode);
            }

            $this->ratioList[$currencyCode] = $ratio;
            ++$row;
        }

        fclose($handle);

        return $this->ratioList;
    }

    /**
     * @throws MoneyException
     *
     * @psalm-param array<string, null|float> $ratioList
     */
    public function saveRatioList(array $ratioList): void
    {
        $dirName = dirname($this->ratioFileName);
        if (!is_dir($dirName)) {
            mkdir($dirName, 0777, true);
        }

        if (($handle = fopen($this->ratioFileName, 'w')) === false) {
            throw new MoneyException("can't open " . $this->ratioFileName . ' for writing');
        }

        foreach ($ratioList as $currencyCode => $ratio) {
            fputcsv($handle, [$currencyCode, $ratio], ';');
        }

        fclose($handle);
        $this->ratioList = $ratioList;
    }
}
