<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    /**
     * Trouver tous les animaux d'un habitat spécifique
     * 
     * @param int $habitatId
     * @return Animal[]
     */
    public function findByHabitat(int $habitatId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.habitat = :habitatId')
            ->setParameter('habitatId', $habitatId)
            ->getQuery()
            ->getResult();
    }
}
