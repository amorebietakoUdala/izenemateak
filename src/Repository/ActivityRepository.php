<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @return Activity[] Returns an array of Activity objects
    */
    public function findByAllActiveActivitys()
    {
        return $this->findByAllActiveActivitysQB()->getQuery()->getResult();
    }

    /**
     * @return ORMQueryBuilder Returns an array of Activity objects
    */
    public function findByAllActiveActivitysQB()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.active = :active')
            ->setParameter('active', true);
    }

    /**
    * @return Registration[] Returns an array of Registration objects
    */
    public function findByOpenAndActiveActivitys() {
        return $this->findByOpenAndActiveActivitysQB()->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns an array of Registration objects
    */
    public function findByOpenAndActiveActivitysQB() {
        $now = (new \DateTime())->format('Y-m-d');
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate <= :today')
            ->andWhere('c.endDate >= :today2')
            ->andWhere('c.active = :active')
            ->andWhere('c.status != '.Activity::STATUS_CLOSED)
            ->setParameter('active', true)
            ->setParameter('today', $now)
            ->setParameter('today2', $now)
            ->orderBy('c.id', 'ASC');
    }

    /**
    * @return Registration[] Returns an array of Registration objects
    */
    public function findByOpenAndActiveActivitysClasification($clasification = null) {
        return $this->findByOpenAndActiveActivitysClasificationQB($clasification)->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns an array of Registration objects
    */
    public function findByOpenAndActiveActivitysClasificationQB($clasification = null) {
        $qb = $this->findByOpenAndActiveActivitysQB();
        if ( $clasification !== null ) {
            $qb->andWhere('c.clasification = :clasification')
            ->setParameter('clasification', $clasification);

        }
        return $qb;
    }

    public function findActivitysBy(array $criteria) {
        $qb = $this->createQueryBuilder('c');
            if ( array_key_exists('active', $criteria) ) {
                $qb->andWhere('c.active = :active')
                    ->setParameter('active', $criteria['active']);
                unset($criteria['active']);
            }
            if ( array_key_exists('startDate', $criteria) ) {
                $qb->andWhere('c.createdAt >= :startDate')
                    ->setParameter('startDate', $criteria['startDate']);
                unset($criteria['startDate']);
            }
            if ( array_key_exists('endDate', $criteria) ) {
                $qb->andWhere('c.createdAt <= :endDate')
                    ->setParameter('endDate', $criteria['endDate']->modify('+1 day'));
                unset($criteria['endDate']);
            }
            foreach ($criteria as $key => $value) {
                $qb->andWhere("c.$key = :$key")
                ->setParameter("$key", $value);
            }
            if ( !array_key_exists('status', $criteria) ) {
                $qb->andWhere("c.status != :status")
                    ->setParameter('status', Activity::STATUS_CLOSED);
            }
        $qb->orderBy('c.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
