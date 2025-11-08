<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use Composer\InstalledVersions;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tbbc\MoneyBundle\TbbcMoneyBundle;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(
        string $environment,
        bool $debug,
        protected array $configs = []
    ) {
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

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework');

        # doctrine-bundle
        if (null !== $doctrineBundleVersion = InstalledVersions::getVersion('doctrine/doctrine-bundle')) {

            # v2
            if (version_compare($doctrineBundleVersion, '3.0.0', '<')) {
                $orm['auto_generate_proxy_classes'] = true;
                $orm['use_savepoints'] = true;
                $orm['report_fields_where_declared'] = true;
                $orm['auto_mapping']['controller_resolver'] = true;
            }

            if (version_compare($doctrineBundleVersion, '2.8.0', '>=')) {
                $orm['enable_lazy_ghost_objects'] = true;
            }

            if (\PHP_VERSION_ID >= 80400 && version_compare($doctrineBundleVersion, '2.15.0', '>=')) {
                $orm['enable_native_lazy_objects'] = true;
            }
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => $orm,
        ]);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yaml');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
}
