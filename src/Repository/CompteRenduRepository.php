<?php

namespace App\Repository;

use App\Entity\CompteRendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteRendu>
 */
class CompteRenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteRendu::class);
    }

    /**
     * @return CompteRendu[]
     */
    public function findByFilters(array $filters)
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($filters['animal'])) {
            $qb->andWhere('c.animal = :animal')
               ->setParameter('animal', $filters['animal']);
        }

        if (!empty($filters['date'])) {
            $qb->andWhere('c.date = :date')
               ->setParameter('date', $filters['date']);
        }

        return $qb->getQuery()->getResult();
    }
}
