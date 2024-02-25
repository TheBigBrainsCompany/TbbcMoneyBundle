<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Tbbc\MoneyBundle\TbbcMoneyBundle;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug, protected array $configs = [])
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            yield new FrameworkBundle(),
            yield new DoctrineBundle(),
            yield new DoctrineMongoDBBundle(),
            yield new TbbcMoneyBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
}
