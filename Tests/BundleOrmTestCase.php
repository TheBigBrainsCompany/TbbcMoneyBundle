<?php
namespace Tbbc\MoneyBundle\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;

class BundleOrmTestCase extends OrmTestCase
{
    protected function createEntityManager(): EntityManager
    {
        $driver = new SimplifiedXmlDriver(array(
            __DIR__ . '/../Resources/config/doctrine/ratios' => 'Tbbc\MoneyBundle\Entity'
        ));

        // create config object
        $config = new Configuration();
        //$config->setMetadataCache(new ArrayCache());
        $config->setMetadataDriverImpl($driver);
        $config->setProxyDir(__DIR__ . '/TestProxies');
        $config->setProxyNamespace('Tbbc\MoneyBundle\Tests\TestProxies');
        $config->setAutoGenerateProxyClasses(true);
        //$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

        // create entity manager
        return EntityManager::create(
            array(
                'driver' => 'pdo_sqlite',
                'path' => "/tmp/sqlite-tbbc-money-test.db"
            ),
            $config
        );
    }

    /**
     * @return DataSet
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__."/_doctrine/dataset/dataset.xml");
    }
}
