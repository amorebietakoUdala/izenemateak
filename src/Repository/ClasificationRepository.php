<?php

namespace App\Repository;

use App\Entity\Clasification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clasification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clasification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clasification[]    findAll()
 * @method Clasification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClasificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clasification::class);
    }

    /**
    * @return Clasification[] Returns an array of Clasification objects
    */
    public function findAllQB()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC');
    }
}
