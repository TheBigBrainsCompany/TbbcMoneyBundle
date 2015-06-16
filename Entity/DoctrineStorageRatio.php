<?php

namespace Tbbc\MoneyBundle\Entity;

class DoctrineStorageRatio
{
    private $id;

    /**
     * @var string
     */
    private $currencyCodePair;

    /**
     * @var integer
     */
    private $ratio;
    
    public function __construct($codePair = null, $ratio = null)
    {
        $this->currencyCodePair = $codePair;
        $this->ratio = $ratio;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param  string $currencyCodePair
     * @return DoctrineStorageRatio
     */
    public function setCurrencyCodePair($currencyCodePair)
    {
        $this->currencyCodePair = $currencyCodePair;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCodePair()
    {
        return $this->currencyCodePair;
    }

    /**
     * get ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * Set ratio
     *
     * @param  float    $ratio
     * @return DoctrineStorageRatio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }
}
