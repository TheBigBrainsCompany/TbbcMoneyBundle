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
            ->willReturn([
                'EUR' => 1.1,
                'USD' => 1.2,
            ]);

        $command = new RatioListCommand($pairManager);
        $tester = new CommandTester($command);
        $tester->execute([]);
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        $output = $tester->getDisplay();
        self::assertStringContainsString('USD;1.2', $output);
    }

    public function testGetRatioListAsTable(): void
    {
        $data = [
            'EUR' => 1.1,
            'USD' => 1.2,
        ];
        $pairManager = $this->createMock(PairManagerInterface::class);
        $pairManager
            ->expects($this->once())
            ->method('getRatioList')
            ->willReturn($data);

        $command = new RatioListCommand($pairManager);
        $tester = new CommandTester($command);
        $tester->execute([
            '--format' => 'table',
        ]);
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertStringContainsString('EUR      | 1.1', $tester->getDisplay());
        self::assertStringContainsString('USD      | 1.2', $tester->getDisplay());
    }

    public function testGetRatioListAsJson(): void
    {
        $data = [
            'EUR' => 1.1,
            'USD' => 1.2,
        ];
        $pairManager = $this->createMock(PairManagerInterface::class);
        $pairManager
            ->expects($this->once())
            ->method('getRatioList')
            ->willReturn($data);

        $command = new RatioListCommand($pairManager);
        $tester = new CommandTester($command);
        $tester->execute([
            '--format' => 'json',
        ]);
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertJson($tester->getDisplay());
        $output = json_decode($tester->getDisplay(), true);
        self::assertSame($data, $output);
    }
}
