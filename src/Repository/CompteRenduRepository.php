<?php
// src/Repository/CompteRenduRepository.php

namespace App\Repository;

use App\Entity\CompteRendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompteRenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteRendu::class);
    }

    /**
     * Méthode pour trouver les comptes rendus selon des filtres spécifiques.
     *
     * @param array $filters
     * @return CompteRendu[]
     */
    public function findByFilters(array $filters)
    {
        $qb = $this->createQueryBuilder('cr');

        if (!empty($filters['animal'])) {
            $qb->andWhere('cr.animal = :animal')
               ->setParameter('animal', $filters['animal']);
        }

        if (!empty($filters['date'])) {
            $qb->andWhere('cr.date = :date')
               ->setParameter('date', new \DateTime($filters['date']));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Exemple de méthode personnalisée pour récupérer les comptes rendus par animal.
     *
     * @param $animalId
     * @return CompteRendu[]
     */
    public function findByAnimal($animalId)
    {
        return $this->createQueryBuilder('cr')
            ->where('cr.animal = :animal')
            ->setParameter('animal', $animalId)
            ->getQuery()
            ->getResult();
    }
}
