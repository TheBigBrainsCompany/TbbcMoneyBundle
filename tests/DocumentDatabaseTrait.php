<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use MongoDB\Client;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

trait DocumentDatabaseTrait
{
    private static array $kernelOptions = [];

    public static function requireMongoDb(): void
    {
        $server = $_ENV['MONGODB_SERVER'] ?? 'mongodb://127.0.0.1:27017';
        try {
            $client = new Client($server, [], [
                'serverSelectionTimeoutMS' => 1000,
            ]);
            $client->listDatabases();
        } catch (ConnectionTimeoutException) {
            self::markTestSkipped('MongoDB server is not available at ' . $server);
        }
    }

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

        $application->run(new ArrayInput([
            'command' => 'doctrine:mongodb:schema:create',
            '--quiet' => true,
        ]), new NullOutput());
    }

    private static function doDropDatabase(): void
    {
        $server = $_ENV['MONGODB_SERVER'] ?? 'mongodb://127.0.0.1:27017';
        $client = new Client($server);
        $client->dropDatabase('default');
    }
}
