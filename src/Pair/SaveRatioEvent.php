<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair;

use DateTimeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class SaveRatioEvent.
 */
class SaveRatioEvent extends Event
{
    public function __construct(
        protected string $referenceCurrencyCode,
        protected string $currencyCode,
        protected float $ratio,
        protected DateTimeInterface $savedAt
    ) {
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }

    public function getReferenceCurrencyCode(): string
    {
        return $this->referenceCurrencyCode;
    }

    public function getSavedAt(): DateTimeInterface
    {
        return $this->savedAt;
    }
}
