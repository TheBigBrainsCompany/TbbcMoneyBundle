<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/07/13
 */

namespace Tbbc\MoneyBundle\Pair\Storage;

use Tbbc\MoneyBundle\Entity\Currency;
use Tbbc\MoneyBundle\Pair\StorageInterface;

use Doctrine\Common\Persistence\ObjectManager;

class DoctrineStorage implements StorageInterface
{

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $objectManager = null;

    /** @var array  */
    protected $ratioList = array();

    /** @var  string */
    protected $referenceCurrencyCode;

    public function __construct(
        ObjectManager $objectManager,
        $referenceCurrencyCode
    )
    {
        $this->objectManager = $objectManager;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * load and return ratioList
     *
     * @param  bool                             $force // force reload (no cache)
     * @throws \Tbbc\MoneyBundle\MoneyException
     */
    public function loadRatioList($force = false)
    {
        if ( ($force === false) && (count($this->ratioList) > 0) ) {
            return $this->ratioList;
        }
        
        $repository = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\Currency');        
        $currencies = $repository->findAll();
        
        // FIXME
        // if filename doesn't exist, init with only reference currency code
        if (0 === count($currencies)) {
            $this->ratioList = array($this->referenceCurrencyCode => (float) 1);
            $this->saveRatioList($this->ratioList);
            return $this->ratioList;
        }
        
        $this->ratioList = array();
        
        foreach ($currencies as $currency) {
            $this->ratioList[$currency->getCode()] = $currency->getRatio();
        }
        
        return $this->ratioList;
    }

    public function saveRatioList($ratioList)
    {
        $currencies = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\Currency')->findAll();
        
        foreach($currencies as $currency) {
            $this->objectManager->remove($currency);
        }
        
        $this->objectManager->flush();
        
        foreach ($ratioList as $code => $ratio) {
            $this->objectManager->persist($this->createCurrency($code, $ratio));
        }

        $this->objectManager->flush();
    }

    /**
     * Create a Currency from code & currency
     *
     * @param string $code
     * @param float  $ratio
     */
    protected function createCurrency($code, $ratio)
    {
        $currency = new Currency();
        $currency->setCode($code);
        $currency->setRatio($ratio);

        return $currency;
    }
}
