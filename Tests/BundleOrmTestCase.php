<?php
namespace Tbbc\MoneyBundle\Tests;

use Doctrine\Common\EventManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use DoctrineExtensions\PHPUnit\OrmTestCase;

class BundleOrmTestCase
    extends OrmTestCase
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function createEntityManager()
    {
        $eventManager = new EventManager();
        $eventManager->addEventListener(array("preTestSetUp"), new SchemaSetupListener());
        
        $driver = new SimplifiedXmlDriver(array(
            __DIR__ . '/../Resources/config/doctrine' => 'Tbbc\MoneyBundle\Entity'
        ));

        // create config object
        $config = new Configuration();
        $config->setMetadataCacheImpl(new ArrayCache());
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
            $config,
            $eventManager
        );
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__."/_doctrine/dataset/dataset.xml");
    }
}
