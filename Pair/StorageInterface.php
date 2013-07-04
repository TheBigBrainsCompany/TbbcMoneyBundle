<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/07/13
 */

namespace Tbbc\MoneyBundle\Pair;


interface StorageInterface {
    /**
     * save ratio list in a storage
     *
     * @param $ratioList
     */
    public function saveRatioList($ratioList);

    /**
     * load ratioList from the storage
     *
     * @param bool $force
     * @return array of type array("EUR"=>1, "USD" => 1.25)
     */
    public function loadRatioList($force = false);
}