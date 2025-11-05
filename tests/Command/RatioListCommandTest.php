<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tbbc\MoneyBundle\Command\RatioListCommand;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

final class RatioListCommandTest extends KernelTestCase
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
        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $output = $tester->getDisplay();
        $this->assertStringContainsString('USD;1.2', $output);
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
        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertStringContainsString('EUR      | 1.1', $tester->getDisplay());
        $this->assertStringContainsString('USD      | 1.2', $tester->getDisplay());
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
        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $this->assertJson($tester->getDisplay());
        $output = json_decode($tester->getDisplay(), true);
        $this->assertSame($data, $output);
    }
}
