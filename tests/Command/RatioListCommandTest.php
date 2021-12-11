<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tbbc\MoneyBundle\Command\RatioListCommand;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class RatioListCommandTest extends KernelTestCase
{
    public function testCanWriteRatioList(): void
    {
        $pairManager = $this->createMock(PairManagerInterface::class);
        $pairManager
            ->expects($this->once())
            ->method('getRatioList')
            ->willReturn(['EUR' => 1.1, 'USD' => 1.2]);

        $command = new RatioListCommand($pairManager);
        $tester = new CommandTester($command);
        $tester->execute([]);
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        $output = $tester->getDisplay();
        self::assertStringContainsString('USD;1.2', $output);
    }
}
