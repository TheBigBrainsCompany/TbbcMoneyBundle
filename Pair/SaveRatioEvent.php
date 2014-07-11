<?php
namespace Tbbc\MoneyBundle\Pair;

use Symfony\Component\EventDispatcher\Event;

class SaveRatioEvent
    extends Event
{
    /**
     * @var string
     */
    protected $referenceCurrencyCode;
    /**
     * @var string
     */
    protected $currencyCode;
    /**
     * @var \DateTime
     */
    protected $savedAt;
    /**
     * @var float
     */
    protected $ratio;

    function __construct(
        $referenceCurrencyCode,
        $currencyCode,
        $ratio,
        $savedAt
    )
    {
        $this->currencyCode = $currencyCode;
        $this->ratio = $ratio;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
        $this->savedAt = $savedAt;
    }

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