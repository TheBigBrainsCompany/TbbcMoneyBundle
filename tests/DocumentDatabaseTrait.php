<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

trait DocumentDatabaseTrait
{
    private static array $kernelOptions = [];

    public function setupDatabase(): void
    {
        self::dropDatabase();
        self::createDatabase();
    }

    public function dropDatabase(): void
    {
        self::doDropDatabase();
    }

    private static function createDatabase(): void
    {
        $kernel = static::createKernel(self::$kernelOptions);
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $code = $application->run(new ArrayInput([
            'command' => 'doctrine:mongodb:schema:create',
            '--quiet' => true,
        ]), new NullOutput());
        self::assertSame(Command::SUCCESS, $code);
    }

    private static function doDropDatabase(): void
    {
        $kernel = static::createKernel(self::$kernelOptions);
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $code = $application->run(new ArrayInput([
            'command' => 'doctrine:mongodb:schema:drop',
            '--quiet' => true,
        ]), new NullOutput());
        self::assertSame(Command::SUCCESS, $code);
    }
}
