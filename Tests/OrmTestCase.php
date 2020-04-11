<?php
/**
 * Adaptation to phpunit: 7+ of https://raw.githubusercontent.com/beberlei/DoctrineExtensions/v0.3.0/lib/DoctrineExtensions/PHPUnit/OrmTestCase.php
 *
 * DoctrineExtensions PHPUnit
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace Tbbc\MoneyBundle\Tests;

use Doctrine\ORM\EntityManager;
use Tbbc\MoneyBundle\Tests\DatabaseTestCase;

abstract class OrmTestCase extends DatabaseTestCase
{
    /**
     * @var EntityManager
     */
    private $_em = null;

    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function setUp(): void
    {
        $this->databaseTester = NULL;
        $tester = $this->getDatabaseTester();

        $tester->setSetUpOperation($this->getSetUpOperation());
        $tester->setDataSet($this->getDataSet());
        $tester->onSetUp();

    }

    /**
     * @return EntityManager
     */
    protected final function getEntityManager()
    {
        if ($this->_em == null) {
            $this->_em = $this->createEntityManager();
            $this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->_em,
                                    "Not a valid Doctrine\ORM\EntityManager returned from createEntityManager() method.");
        }
        return $this->_em;
    }

    /**
     * @return EntityManager
     */
    abstract protected function createEntityManager();

    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    final protected function getDoctrineConnection()
    {
        $em = $this->getEntityManager();

        return $em->getConnection();
    }
//
//    /**
//     * Creates a IDatabaseTester for this testCase.
//     *
//     * @return DatabaseTester
//     */
//    protected function newDatabaseTester()
//    {
//        return new DatabaseTester($this->getConnection());
//    }
}
