<?php

namespace App\Repository;

use App\Entity\RegistrationExtraField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistrationExtraField>
 *
 * @method RegistrationExtraField|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistrationExtraField|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistrationExtraField[]    findAll()
 * @method RegistrationExtraField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationExtraFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationExtraField::class);
    }

    public function add(RegistrationExtraField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegistrationExtraField $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
