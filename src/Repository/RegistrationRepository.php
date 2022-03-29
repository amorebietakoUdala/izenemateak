<?php

namespace App\Repository;

use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    /**
    * @return Registration[] Returns an array of Registration objects
    */
    public function findByActiveCourses() {
        return $this->findByActiveCoursesQB()->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder Returns an array of Registration objects
    */
    public function findByActiveCoursesQB() {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.course', 'c')
            ->andWhere('c.active = :active')
            ->setParameter('active', true)
            ->orderBy('r.id', 'ASC');
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
        $now = new \DateTime();
        return $this->createQueryBuilder('r')
            ->innerJoin('r.course', 'c')
            ->andWhere('c.startDate' <= ':today')
            ->andWhere('c.endDate' > ':today2')
            ->andWhere('c.active = :active')
            ->setParameter('active', true)
            ->setParameter('today', $now)
            ->setParameter('today2', $now)
            ->orderBy('r.id', 'ASC');
    }

    /**
     * @return Registration|null Returns an array of Registration objects
     */
    public function findOneByDniCourse($dni, $course)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dni = :dni')
            ->andWhere('r.course = :course')
            ->setParameter('dni', $dni)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Registration[]|null Returns not confirmed and not fortunate registrations
     */
    public function findNotConfirmedAndNotFortunate($course)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.course = :course')
            ->andWhere('r.fortunate = :fortunate')
            ->andWhere('r.confirmed IS NULL')
            ->setParameter('course', $course)
            ->setParameter('fortunate', false)
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Registration|null Returns the next on WaitingList
     */
    public function findNextOnWaitingListCourse($course) {
        $result = $this->createQueryBuilder('r')
            ->andWhere('r.course = :course')
            ->andWhere('r.fortunate = :fortunate')
            ->andWhere('r.confirmed IS NULL')
            ->setParameter('course', $course)
            ->setParameter('fortunate', false)
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();
        if (count($result) === 0) {
            return null;
        }
        return $result[0];
    }

    /**
     * @return Registration|null Returns an array of Registration objects
     */
    public function findOneByNameSurnamesCourse($name, $surname1, $surname2, $course) {
        return $this->createQueryBuilder('r')
            ->andWhere('r.name = :name')
            ->andWhere('r.surname1 = :surname1')
            ->andWhere('r.surname2 = :surname2')
            ->andWhere('r.course = :course')
            ->setParameter('name', $name)
            ->setParameter('surname1', $surname1)
            ->setParameter('surname2', $surname2)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByNameSurname1DateOfBirthCourse($name,$surname1, $dateOfBirth, $course) {
        return $this->createQueryBuilder('r')
            ->andWhere('r.name = :name')
            ->andWhere('r.surname1 = :surname1')
            ->andWhere('r.dateOfBirth = :dateOfBirth')
            ->andWhere('r.course = :course')
            ->setParameter('name', $name)
            ->setParameter('surname1', $surname1)
            ->setParameter('dateOfBirth', $dateOfBirth)
            ->setParameter('course', $course)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findRegistrationsBy(array $criteria) {
        $qb = $this->createQueryBuilder('r')
            ->innerJoin('r.course', 'c');
            if ( array_key_exists('active', $criteria) ) {
                $qb->andWhere('c.active = :active')
                    ->setParameter('active', $criteria['active']);
                unset($criteria['active']);
            }
            if ( array_key_exists('startDate', $criteria) ) {
                $qb->andWhere('r.createdAt >= :startDate')
                    ->setParameter('startDate', $criteria['startDate']);
                unset($criteria['startDate']);
            }
            if ( array_key_exists('endDate', $criteria) ) {
                $qb->andWhere('r.createdAt <= :endDate')
                    ->setParameter('endDate', $criteria['endDate']->modify('+1 day'));
                unset($criteria['endDate']);
            }
            foreach ($criteria as $key => $value) {
                $qb->andWhere("r.$key = :$key")
                ->setParameter("$key", $value);
            }
        $qb->orderBy('r.id', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Person
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
