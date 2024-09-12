<?php

namespace App\Repository;

use App\Entity\Habitat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Habitat>
 */
class HabitatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Habitat::class);
    }

    /**
     * Récupère tous les habitats avec leurs animaux associés
     * 
     * @return Habitat[] Returns an array of Habitat objects
     */
    public function findAllWithAnimals(): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.animals', 'a')
            ->addSelect('a') // Sélectionne aussi les animaux associés
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve un habitat par son ID avec ses animaux
     *
     * @param int $id
     * @return Habitat|null
     */
    public function findOneWithAnimals(int $id): ?Habitat
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.animals', 'a')
            ->addSelect('a')
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
