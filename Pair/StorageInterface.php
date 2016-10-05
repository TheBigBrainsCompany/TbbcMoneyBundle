<?php
namespace Tbbc\MoneyBundle\Pair;

/**
 * Interface StorageInterface
 * @package Tbbc\MoneyBundle\Pair
 * @author Philippe Le Van.
 */
interface StorageInterface
{
    /**
     * save ratio list in a storage
     *
     * @param array $ratioList
     */
    public function saveRatioList($ratioList);

    /**
     * load ratioList from the storage
     *
     * @param bool $force
     *
     * @return array of type array("EUR"=>1, "USD" => 1.25)
     */
    public function loadRatioList($force = false);
}
