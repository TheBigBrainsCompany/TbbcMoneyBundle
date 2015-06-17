<?php
namespace Tbbc\MoneyBundle\PairHistory;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Tbbc\MoneyBundle\Entity\RatioHistory;
use Tbbc\MoneyBundle\Pair\SaveRatioEvent;
use Tbbc\MoneyBundle\Utils\CurrencyUtils;

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
    public function getRatioAtDate($currencyTo, \DateTime $savedAt, $currencyFrom = null)
    {
        $currencyCode = CurrencyUtils::isCurrency($currencyTo) ? $currencyTo->getName() : $currencyTo;
        $referenceCurrencyCode = null === $currencyFrom
            ? $this->referenceCurrencyCode
            : (CurrencyUtils::isCurrency($currencyFrom) ? $currencyFrom->getName() : $currencyFrom);

        if ($currencyCode === $referenceCurrencyCode) {
            return 1.;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select('rh')
            ->from('\Tbbc\MoneyBundle\Entity\RatioHistory', 'rh')
            ->where('rh.currencyCode = :currencyCode')
            ->andWhere('rh.referenceCurrencyCode = :referenceCurrencyCode')
            ->andWhere('rh.savedAt <= :historyDate')
            ->orderBy('rh.savedAt', 'DESC')
            ->setParameter('historyDate', $savedAt)
            ->setParameter('currencyCode', $currencyCode)
            ->setParameter('referenceCurrencyCode', $referenceCurrencyCode)
            ->setMaxResults(1)
        ;
        $query = $qb->getQuery();
        try {
            $ratioHistory = $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
        return $ratioHistory->getRatio();
    }

    /**
     * @inheritdoc
     */
    public function getRatioHistory($currencyTo, $startDate, $endDate, $currencyFrom = null)
    {
        $currencyCode = CurrencyUtils::isCurrency($currencyTo) ? $currencyTo->getName() : $currencyTo;
        $referenceCurrencyCode = null === $currencyFrom
            ? $this->referenceCurrencyCode
            : (CurrencyUtils::isCurrency($currencyFrom) ? $currencyFrom->getName() : $currencyFrom);

        $qb = $this->em->createQueryBuilder();
        $qb->select('rh')
            ->from('\Tbbc\MoneyBundle\Entity\RatioHistory', 'rh')
            ->where('rh.currencyCode = :currencyCode')
            ->andWhere('rh.referenceCurrencyCode = :referenceCurrencyCode')
            ->orderBy('rh.savedAt', 'ASC')
            ->setParameter('currencyCode', $currencyCode)
            ->setParameter('referenceCurrencyCode', $referenceCurrencyCode)
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