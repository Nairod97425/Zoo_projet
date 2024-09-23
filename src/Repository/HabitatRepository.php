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
     * Récupère tous les habitats avec leurs avis et animaux associés
     * 
     * @return Habitat[] Returns an array of Habitat objects
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.Avis', 'a')
            ->leftJoin('h.animals', 'an')
            ->addSelect('a', 'an') // Sélectionne aussi les avis et les animaux associés
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve un habitat par son ID avec ses avis et animaux
     *
     * @param int $id
     * @return Habitat|null
     */
    public function findOneWithRelations(int $id): ?Habitat
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.Avis', 'a')
            ->leftJoin('h.animals', 'an')
            ->addSelect('a', 'an')
            ->where('h.id_habitat = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Sauvegarde l'entité Habitat
     *
     * @param Habitat $habitat
     * @param bool $flush Si true, appelle flush() après la persistance
     */
    public function save(Habitat $habitat, bool $flush = true): void
    {
        $this->getEntityManager()->persist($habitat);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
