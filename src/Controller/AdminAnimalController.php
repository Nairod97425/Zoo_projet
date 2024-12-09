<?php
// src/Controller/AdminAnimalController.php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;

class AdminAnimalController extends AbstractController
{
    private $entityManager;

    // Injection du service EntityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin_animal', name: 'animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        // Vérifier si l'utilisateur a le rôle ROLE_EMPLOYE
        if (!$this->isGranted('ROLE_EMPLOYE', 'ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit, vous devez être un employé pour accéder à cette page.');
        }

        // Récupérer tous les animaux depuis le repository
        $animals = $animalRepository->findAll();

        return $this->render('admin_animal/index.html.twig', [
            'animals' => $animals,
        ]);
    }

    #[Route('/admin_animal/create', name: 'animal_new', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        // Vérifier si l'utilisateur a le rôle ROLE_EMPLOYE
        if (!$this->isGranted('ROLE_EMPLOYE', 'ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit, vous devez être un employé pour accéder à cette page.');
        }

        // Créer un nouvel objet Animal
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $animal->setImage($newFilename); // Enregistrer le nom du fichier dans l'entité
            }

            // Persister l'entité Animal
            $this->entityManager->persist($animal);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'animal a été ajouté avec succès.');

            return $this->redirectToRoute('animal_index');
        }

        return $this->render('admin_animal/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin_animal/edit/{id}', name: 'animal_edit', methods: ['GET', 'POST'])]
    public function edit(Animal $animal, Request $request): Response
    {
        // Vérifier si l'utilisateur a le rôle ROLE_EMPLOYE
        if (!$this->isGranted('ROLE_EMPLOYE', 'ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit, vous devez être un employé pour accéder à cette page.');
        }

        // Créer le formulaire pour modifier un animal
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gérer l'upload de l'image (si une nouvelle image est téléchargée)
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $animal->setImage($newFilename); // Mettre à jour le nom de l'image dans l'entité
            }

            // Mettre à jour l'entité Animal
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'animal a été modifié avec succès.');

            return $this->redirectToRoute('animal_index');
        }

        return $this->render('admin_animal/edit.html.twig', [
            'form' => $form->createView(),
            'animal' => $animal,
        ]);
    }

    #[Route('/admin_animal/delete/{id}', name: 'animal_delete', methods: ['POST'])]
    public function delete(Animal $animal, Request $request): Response
    {
        // Vérifier si l'utilisateur a le rôle ROLE_EMPLOYE
        if (!$this->isGranted('ROLE_EMPLOYE', 'ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit, vous devez être un employé pour accéder à cette page.');
        }

        // Vérification du token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {
            // Supprimer l'animal
            $this->entityManager->remove($animal);
            $this->entityManager->flush();

            $this->addFlash('success', 'L\'animal a été supprimé avec succès.');
        }

        return $this->redirectToRoute('animal_index');
    }
}
