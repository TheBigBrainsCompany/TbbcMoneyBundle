<?php
namespace Tbbc\MoneyBundle\Tests\PairHistory;

use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\SaveRatioEvent;
use Tbbc\MoneyBundle\PairHistory\PairHistoryManager;
use Tbbc\MoneyBundle\Tests\BundleOrmTestCase;

/**
 * @group historyManager
 */
class PairHistoryManagerTest extends BundleOrmTestCase
{
    /**
     * @var PairHistoryManager
     */
    protected $pairHistoryManager;

    protected $ratioHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $em = $this->getEntityManager();
        $this->pairHistoryManager = new PairHistoryManager(
            $em,
            'EUR'
        );
        $this->ratioHistoryRepo = $em->getRepository('Tbbc\MoneyBundle\Entity\RatioHistory');
    }

    public function testSaveRatioHistory()
    {
        $event = new SaveRatioEvent('EUR', 'USD', 1.25, new \DateTime('2012-07-08 12:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $ratioHistoryList = $this->ratioHistoryRepo->findAll();
        $this->assertEquals(1, count($ratioHistoryList));

        $event = new SaveRatioEvent('EUR', 'USD', 1.50, new \DateTime('2012-07-08 13:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $ratioHistoryList = $this->ratioHistoryRepo->findAll();
        $this->assertEquals(2, count($ratioHistoryList));
    }

    public function testGetRatioList()
    {
        $event = new SaveRatioEvent('EUR', 'USD', 1.25, new \DateTime('2012-07-08 12:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('EUR', 'USD', 1.50, new \DateTime('2012-07-08 13:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('EUR', 'USD', 1.75, new \DateTime('2012-07-08 14:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);

        $ratioList = $this->pairHistoryManager->getRatioHistory('USD', null, null);
        $this->assertEquals(3, count($ratioList));
        $this->assertEquals(1.25, $ratioList[0]["ratio"]);
        $this->assertEquals(1.50, $ratioList[1]["ratio"]);
        $this->assertEquals(1.75, $ratioList[2]["ratio"]);
        $this->assertEquals('2012-07-08 12:00:00', $ratioList[0]["savedAt"]->format('Y-m-d H:i:s'));
        $this->assertEquals('2012-07-08 13:00:00', $ratioList[1]["savedAt"]->format('Y-m-d H:i:s'));
        $this->assertEquals('2012-07-08 14:00:00', $ratioList[2]["savedAt"]->format('Y-m-d H:i:s'));

        $ratioList = $this->pairHistoryManager->getRatioHistory('USD',new \DateTime('2012-07-08 12:30:00') , null);
        $this->assertEquals(2, count($ratioList));
        $ratioList = $this->pairHistoryManager->getRatioHistory('USD',new \DateTime('2012-07-08 12:30:00') , new \DateTime('2012-07-08 13:30:00'));
        $this->assertEquals(1, count($ratioList));
    }
    public function testGetRatio()
    {
        $event = new SaveRatioEvent('EUR', 'USD', 1.25, new \DateTime('2012-07-08 12:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('EUR', 'USD', 1.50, new \DateTime('2012-07-08 13:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('EUR', 'USD', 1.75, new \DateTime('2012-07-08 14:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);

        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-08 12:30:00'));
        $this->assertEquals(1.25, $ratio);
        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-08 13:30:00'));
        $this->assertEquals(1.50, $ratio);
        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-10 12:30:00'));
        $this->assertEquals(1.75, $ratio);
        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2011-07-10 12:30:00'));
        $this->assertEquals(null, $ratio);

        $ratio = $this->pairHistoryManager->getRatioAtDate('EUR', new \DateTime('2011-07-10 12:30:00'));
        $this->assertEquals(1, $ratio);
        $this->assertTrue(is_float($ratio));
    }

    /**
     *
     */
    public function testGetRatioException()
    {
        $event = new SaveRatioEvent('EUR', 'USD', 1.25, new \DateTime('2012-07-08 12:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('CAD', 'USD', 1.50, new \DateTime('2012-07-08 13:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);
        $event = new SaveRatioEvent('EUR', 'USD', 1.75, new \DateTime('2012-07-08 14:00:00'));
        $this->pairHistoryManager->listenSaveRatioEvent($event);

        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-08 12:30:00'));
        $this->assertEquals(1.25, $ratio);
        try {
            $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-08 13:30:00'));
            $this->fail('should throw an exception du to reference currency code');
        } catch (MoneyException $e) {
            $this->assertTrue(true);
        }
        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2012-07-10 12:30:00'));
        $this->assertEquals(1.75, $ratio);
        $ratio = $this->pairHistoryManager->getRatioAtDate('USD', new \DateTime('2011-07-10 12:30:00'));
        $this->assertEquals(null, $ratio);
    }
}
