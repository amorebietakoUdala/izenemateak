<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @return Course[] Returns an array of Course objects
    */
    public function findByAllActiveCourses()
    {
        return $this->findByAllActiveCoursesQB()->getQuery()->getResult();
    }

    /**
     * @return ORMQueryBuilder Returns an array of Course objects
    */
    public function findByAllActiveCoursesQB()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.active = :active')
            ->setParameter('active', true);
    }

    /**
    * @return Registration[] Returns an array of Registration objects
    */
    public function findByOpenAndActiveCourses() {
        return $this->findByOpenAndActiveCoursesQB()->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns an array of Registration objects
    */
    public function findByOpenAndActiveCoursesQB() {
        $now = (new \DateTime())->format('Y-m-d');
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate <= :today')
            ->andWhere('c.endDate >= :today2')
            ->andWhere('c.active = :active')
            ->andWhere('c.status != '.Course::STATUS_CLOSED)
            ->setParameter('active', true)
            ->setParameter('today', $now)
            ->setParameter('today2', $now)
            ->orderBy('c.id', 'ASC');
    }

    /**
    * @return Registration[] Returns an array of Registration objects
    */
    public function findByOpenAndActiveCoursesClasification($clasification = null) {
        return $this->findByOpenAndActiveCoursesClasificationQB($clasification)->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns an array of Registration objects
    */
    public function findByOpenAndActiveCoursesClasificationQB($clasification = null) {
        $qb = $this->findByOpenAndActiveCoursesQB();
        if ( $clasification !== null ) {
            $qb->andWhere('c.clasification = :clasification')
            ->setParameter('clasification', $clasification);

        }
        return $qb;
    }

    public function findCoursesBy(array $criteria) {
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
        $qb->orderBy('c.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
