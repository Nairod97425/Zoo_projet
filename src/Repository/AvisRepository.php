<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Récupère les avis approuvés pour un habitat
     * 
     * @param int $habitatId
     * @return Avis[] Returns an array of approved Avis objects
     */
    public function findApprovedByHabitat(int $habitatId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.habitat = :habitatId')
            ->andWhere('a.isApproved = true')
            ->setParameter('habitatId', $habitatId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les avis non approuvés pour validation
     * 
     * @return Avis[] Returns an array of Avis objects awaiting approval
     */
    public function findPendingApproval(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.isApproved = false')
            ->getQuery()
            ->getResult();
    }
}
