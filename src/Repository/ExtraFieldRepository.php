<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ExtraField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExtraField>
 *
 * @method ExtraField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtraField[]    findAll()
 * @method ExtraField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtraFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExtraField::class);
    }

    public function add(ExtraField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExtraField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return ExtraField[] Returns an array of ExtraField objects
    */
    public function findByNameLike($value, $locale = 'es')
    {
        $qb = $this->createQueryBuilder('ef');
        if ($locale === 'es') {
            $qb->andWhere('ef.name LIKE :val')
               ->setParameter('val', '%'.$value.'%');
        } else {
            $qb->andWhere('ef.nameEu LIKE :val')
               ->setParameter('val', '%'.$value.'%');
        }
        return $qb->getQuery()->getResult();
    }

    /**
    * @return ExtraField[] Returns an array of ExtraField objects
    */
    public function findByActivity(Activity $activity) {
        $qb = $this->findByActivityQB($activity);
        return $qb->getQuery()->getResult();

    }

    /**
    * @return QueryBuilder Returns an array of ExtraField objects
    */
    public function findByActivityQB(?Activity $activity) {
        $qb = $this->createQueryBuilder('ef')
            ->innerJoin('ef.activities', 'a');
        if ( null !== $activity) {
            $qb->andWhere('a.id = :activity')
            ->setParameter('activity', $activity);
        }
        return $qb;

    }
}
