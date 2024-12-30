<?php

namespace App\Service;

use App\Repository\HabitatRepository;

class HabitatService
{
    // Méthodes pour gérer les opérations liées aux habitats
    private HabitatRepository $habitatRepository;

    // Injection du repository dans le constructeur
    public function __construct(HabitatRepository $habitatRepository)
    {
        $this->habitatRepository = $habitatRepository;
    }

    // Méthode pour récupérer tous les habitats
    public function getAllHabitats()
    {
        return $this->habitatRepository->findAll(); // Récupération de tous les habitats
    }
}