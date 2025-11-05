<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Document;

use DateTime;
use DateTimeInterface;

class DocumentRatioHistory
{
    protected ?string $id = null;
    protected string $referenceCurrencyCode = '';
    protected string $currencyCode = '';
    protected float $ratio = 0;
    protected DateTimeInterface $savedAt;

    public function __construct()
    {
        $this->savedAt = new DateTime();
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setRatio(float $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }

    public function setReferenceCurrencyCode(string $referenceCurrencyCode): self
    {
        $this->referenceCurrencyCode = $referenceCurrencyCode;

        return $this;
    }

    public function getReferenceCurrencyCode(): string
    {
        return $this->referenceCurrencyCode;
    }

    public function setSavedAt(DateTimeInterface $savedAt): self
    {
        $this->savedAt = $savedAt;

        return $this;
    }

    public function getSavedAt(): DateTimeInterface
    {
        return $this->savedAt;
    }
}
