<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tbbc\MoneyBundle\Command\RatioSaveCommand;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

final class RatioSaveCommandTest extends TestCase
{
    private MockObject|PairManagerInterface $pairManager;

    protected function setUp(): void
    {
        $this->pairManager = $this->createMock(PairManagerInterface::class);
    }

    public function testWillWriteOk(): void
    {
        $this->pairManager
            ->expects($this->once())
            ->method('saveRatio')
            ->with('EUR', '1.2563');

        $command = new RatioSaveCommand($this->pairManager);
        $tester = new CommandTester($command);
        $tester->execute([
            'currencyCode' => 'EUR',
            'ratio' => '1.2563',
        ]);
        $this->assertSame(Command::SUCCESS, $tester->getStatusCode());
        $output = $tester->getDisplay();
        $this->assertSame("ratio saved\n", $output);
    }

    public function testWillWriteExceptionMessage(): void
    {
        $this->pairManager
            ->expects($this->once())
            ->method('saveRatio')
            ->with('EUR', '1.2563')
            ->willThrowException(new MoneyException('test'));

        $command = new RatioSaveCommand($this->pairManager);
        $tester = new CommandTester($command);
        $tester->execute([
            'currencyCode' => 'EUR',
            'ratio' => '1.2563',
        ]);
        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $output = $tester->getDisplay();
        $this->assertSame("ERROR : ratio no saved du to error : test\n", $output);
    }
}
