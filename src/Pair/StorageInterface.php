<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair;

/**
 * Interface StorageInterface.
 *
 * @author Philippe Le Van.
 */
interface StorageInterface
{
    /**
     * save ratio list in a storage.
     *
     * @psalm-param array<string, null|float> $ratioList
     */
    public function saveRatioList(array $ratioList): void;

    /**
     * load ratioList from the storage.
     *
     * @return array of type array("EUR"=>1, "USD" => 1.25)
     * @psalm-return array<string, null|float>
     */
    public function loadRatioList(bool $force = false): array;
}
