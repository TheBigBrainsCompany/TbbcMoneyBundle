<?php

namespace Tbbc\MoneyBundle\Entity;

class Currency
{
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $ratio;
    
    public function __construct($code = null, $ratio = null) 
    {
        $this->code = $code;
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
     * @param  string $code
     * @return Currency
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
