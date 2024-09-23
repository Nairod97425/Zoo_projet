<?php
// src/Controller/AnimalController.php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController extends AbstractController
{
    private $entityManager;

    // On injecte l'EntityManager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/animaux', name: 'animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        $animals = $animalRepository->findAll();
        return $this->render('animal/index.html.twig', [
            'animals' => $animals,
        ]);
    }

    #[Route('/animaux/new', name: 'animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($animal);
            $this->entityManager->flush();

            return $this->redirectToRoute('animal_index');
        }

        return $this->render('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/animaux/edit/{id}', name: 'animal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animal $animal): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('animal_index');
        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/animaux/delete/{id}', name: 'animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal): Response
    {
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($animal);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('animal_index');
    }
}
