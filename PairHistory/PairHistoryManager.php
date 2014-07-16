<?php
namespace Tbbc\MoneyBundle\PairHistory;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Tbbc\MoneyBundle\Entity\RatioHistory;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\SaveRatioEvent;

class PairHistoryManager
    implements PairHistoryManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $referenceCurrencyCode;

    public function __construct(
        EntityManager $em,
        $referenceCurrencyCode
    )
    {
        $this->em = $em;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * @inheritdoc
     */
    public function getRatioAtDate($currencyCode, \DateTime $historyDate)
    {
        if ($currencyCode == $this->referenceCurrencyCode) {
            return (float)1;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('rh')
            ->from('\Tbbc\MoneyBundle\Entity\RatioHistory', 'rh')
            ->where('rh.currencyCode = :currencyCode')
            ->orderBy('rh.savedAt', 'DESC')
            ->andWhere('rh.savedAt <= :historyDate')
            ->setParameter('historyDate', $historyDate)
            ->setParameter('currencyCode', $currencyCode)
            ->setMaxResults(1)
        ;
        $query = $qb->getQuery();
        try {
            $ratioHistory = $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
        if ($ratioHistory->getReferenceCurrencyCode() !== $this->referenceCurrencyCode) {
            throw new MoneyException('Reference currency code changed in history of currency ratio');
        }
        return $ratioHistory->getRatio();
    }

    /**
     * @inheritdoc
     */
    public function getRatioHistory($currencyCode, $startDate=null, $endDate=null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('rh')
            ->from('\Tbbc\MoneyBundle\Entity\RatioHistory', 'rh')
            ->where('rh.currencyCode = :currencyCode')
            ->andWhere('rh.referenceCurrencyCode = :referenceCurrencyCode')
            ->orderBy('rh.savedAt', 'ASC')
            ->setParameter('currencyCode', $currencyCode)
            ->setParameter('referenceCurrencyCode', $this->referenceCurrencyCode)
        ;
        if ($startDate instanceof \DateTime) {
            $qb->andWhere('rh.savedAt >= :startDate')
                ->setParameter('startDate', $startDate);
        }
        if ($endDate instanceof \DateTime) {
            $qb->andWhere('rh.savedAt <= :endDate')
                ->setParameter('endDate', $endDate);
        }
        $query = $qb->getQuery();
        $resultList = $query->getResult();
        $res = array();
        foreach($resultList as $ratioHistory) {
            $res[] = array(
                'ratio' => $ratioHistory->getRatio(),
                'savedAt' => $ratioHistory->getSavedAt()
            );
        }
        return $res;
    }

    /**
     * @inheritdoc
     */
    public function listenSaveRatioEvent(SaveRatioEvent $event)
    {

        $ratioHistory = new RatioHistory();
        $ratioHistory->setReferenceCurrencyCode($event->getReferenceCurrencyCode());
        $ratioHistory->setCurrencyCode($event->getCurrencyCode());
        $ratioHistory->setRatio($event->getRatio());
        $ratioHistory->setSavedAt($event->getSavedAt());
        $this->em->persist($ratioHistory);
        $this->em->flush();
    }


} 