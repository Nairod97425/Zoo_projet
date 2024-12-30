<?php
// src/Repository/ConsultationRepository.php

namespace App\Repository;

use App\Entity\Consultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function findByFilters(array $filters)
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($filters['animal'])) {
            $qb->andWhere('c.animal = :animal')
               ->setParameter('animal', $filters['animal']);
        }

        if (!empty($filters['date'])) {
            $qb->andWhere('c.date = :date')
               ->setParameter('date', new \DateTime($filters['date']));
        }

        return $qb->getQuery()->getResult();
    }

    public function updateHealthStatus(Consultation $consultation, string $healthStatus): void
    {
        if ($consultation->getUser()->getRoles() === 'ROLE_VETERINAIRE') {
            $consultation->setHealthStatus($healthStatus);
            $this->_em->persist($consultation);
            $this->_em->flush();
        } else {
            throw new \Exception('Unauthorized access: User must be a veterinarian.');
        }
    }
}