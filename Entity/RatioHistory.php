<?php
namespace Tbbc\MoneyBundle\Entity;


class RatioHistory
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var string
     */
    protected $referenceCurrencyCode;
    /**
     * @var string
     */
    protected $currencyCode;
    /**
     * @var float
     */
    protected $ratio;
    /**
     * @var \DateTime
     */
    protected $savedAt;

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param float $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param string $referenceCurrencyCode
     */
    public function setReferenceCurrencyCode($referenceCurrencyCode)
    {
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * @return string
     */
    public function getReferenceCurrencyCode()
    {
        return $this->referenceCurrencyCode;
    }

    /**
     * @param \DateTime $savedAt
     */
    public function setSavedAt($savedAt)
    {
        $this->savedAt = $savedAt;
    }

    /**
     * @return \DateTime
     */
    public function getSavedAt()
    {
        return $this->savedAt;
    }


} 