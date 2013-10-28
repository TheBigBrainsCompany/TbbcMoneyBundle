<?php

namespace Tbbc\MoneyBundle\Entity;

class DoctrineStorageRatio
{
    private $id;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var integer
     */
    private $ratio;
    
    public function __construct($code = null, $ratio = null) 
    {
        $this->currencyCode = $code;
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
     * @param  string $currencyCode
     * @return Currency
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
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
     * @return Currency
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }
}
