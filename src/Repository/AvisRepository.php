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
     * Récupère tous les avis associés à un habitat spécifique
     *
     * @param int $habitatId
     * @return Avis[]
     */
    public function findByHabitat(int $habitatId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.habitat = :habitatId')
            ->setParameter('habitatId', $habitatId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un avis spécifique avec l'habitat associé
     *
     * @param int $id
     * @return Avis|null
     */
    public function findOneWithHabitat(int $id): ?Avis
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.habitat', 'h')
            ->addSelect('h')
            ->where('a.id_Avis = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
