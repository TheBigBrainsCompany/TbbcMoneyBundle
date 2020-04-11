<?php
/**
 * Adaptation to phpunit: 7+ of https://raw.githubusercontent.com/beberlei/DoctrineExtensions/v0.3.0/lib/DoctrineExtensions/PHPUnit/DatabaseTestCase.php
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

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\Operation\Operation;
use PHPUnit\DbUnit\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var Connection
     */
    private static $_connection = null;

    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    abstract protected function getDoctrineConnection();

    /**
     * @return Connection
     */
    final protected function getConnection()
    {
        if (self::$_connection == null) {
            self::$_connection = new DefaultConnection($this->getDoctrineConnection()->getWrappedConnection());
        }
        return self::$_connection;
    }

    /**
     * Returns the database operation executed in test setup.
     *
     * @return Operation
     */
    protected function getSetUpOperation()
    {
        return Factory::CLEAN_INSERT();
    }
}
