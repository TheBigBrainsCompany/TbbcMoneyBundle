<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Money;

use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Money\MoneyManager;

class MoneyManagerTest extends TestCase
{
    private MoneyManager $manager;

    public function setUp(): void
    {
        $this->manager = new MoneyManager('EUR', 2);
    }

    public function testCreateMoneyFromFloat(): void
    {
        $money = $this->manager->createMoneyFromFloat(2.5);
        $this->assertSame('EUR', $money->getCurrency()->getCode());
        $this->assertSame('250', $money->getAmount());

        $money = $this->manager->createMoneyFromFloat(2.5, 'USD');
        $this->assertSame('USD', $money->getCurrency()->getCode());
        $this->assertSame('250', $money->getAmount());

        $money = $this->manager->createMoneyFromFloat(2.49999999999999);
        $this->assertSame('250', $money->getAmount());

        $money = $this->manager->createMoneyFromFloat(2.529999999999);
        $this->assertSame('253', $money->getAmount());
    }
}
