<?php
// src/Controller/AdminController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Habitat;
use App\Form\HabitatType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\HabitatService;
use App\Service\FileUploaderAnimal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private HabitatService $habitatService;
    private UserPasswordHasherInterface $passwordHasher;
    private FileUploaderAnimal $fileUploader;

    public function __construct(EntityManagerInterface $entityManager, HabitatService $habitatService, UserPasswordHasherInterface $passwordHasher, FileUploaderAnimal $fileUploader)
    {
        $this->entityManager = $entityManager;
        $this->habitatService = $habitatService;
        $this->passwordHasher = $passwordHasher;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/admin/user/create_user', name: 'admin_create_user', methods: ['GET', 'POST'])]
    public function createUser(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('admin_list_users');
        }

        return $this->render('admin/user/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/edit_user/{id}', name: 'admin_edit_user', methods: ['GET', 'POST'])]
    public function editUser(int $id, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            if ($newPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('admin_list_users');
        }

        return $this->render('admin/user/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'admin_delete_user', methods: ['POST'])]
    public function deleteUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'Utilisateur supprimé avec succès');
        return $this->redirectToRoute('admin_list_users');
    }

    #[Route('/admin/user/list_users', name: 'admin_list_users')]
    public function listUsers(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/user/list_users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/habitats', name: 'admin_habitats', methods: ['GET'])]
    public function manageHabitats(): Response
    {
        $habitats = $this->habitatService->getAllHabitats();

        return $this->render('habitat/liste.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    #[Route('/admin/habitat/create', name: 'admin_create_habitat', methods: ['GET', 'POST'])]
    public function createHabitat(Request $request): Response
    {
        $habitat = new Habitat("Nom de l'Habitat");
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->handleFileUpload($file);
                        $filenames[] = $filename;
                    }
                }
                $habitat->setImages($filenames);
            }

            $this->entityManager->persist($habitat);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'habitat a été créé avec succès.');
            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function handleFileUpload(UploadedFile $file): string
    {
        $uploadsDirectory = $this->getParameter('uploads_directory');
        $filename = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDirectory, $filename);
        } catch (IOExceptionInterface $exception) {
            $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
            throw $exception;
        }

        return $filename;
    }

    #[Route('/admin/habitat/edit/{id}', name: 'admin_edit_habitat', methods: ['GET', 'POST'])]
    public function editHabitat(Habitat $habitat, Request $request): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Habitat modifié avec succès.');
            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/edit.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    #[Route('/admin/animal/manage_animals', name: 'animal_manage', methods: ['GET'])]
    public function manageAnimals(AnimalRepository $animalRepository): Response
    {
        $animals = $animalRepository->findAll();
        return $this->render('admin/animal/manage_animals.html.twig', [
            'animals' => $animals,
        ]);
    }

    #[Route('/admin/animal/create', name: 'admin_animal_new', methods: ['GET', 'POST'])]
    public function createAnimal(Request $request): Response
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('image')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->fileUploader->upload($file);
                        $filenames[] = $filename;
                    }
                }
                $animal->setImage($filenames);
            }

            $this->entityManager->persist($animal);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'animal a été ajouté avec succès.');

            return $this->redirectToRoute('animal_manage');
        }

        return $this->render('admin/animal/create_animal.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/animal/edit/{id}', name: 'admin_animal_edit', methods: ['GET', 'POST'])]
    public function editAnimal(Animal $animal, Request $request): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('image')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->fileUploader->upload($file);
                        $filenames[] = $filename;
                    }
                }

                // Supprimer les anciennes images si nécessaire
                $existingImages = $animal->getImage();
                foreach ($existingImages as $oldImage) {
                    $oldImagePath = $this->getParameter('uploads_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $animal->setImage($filenames);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'L\'animal a été modifié avec succès.');

            return $this->redirectToRoute('animal_manage');
        }

        return $this->render('admin/animal/edit_animal.html.twig', [
            'form' => $form->createView(),
            'animal' => $animal,
        ]);
    }

    #[Route('/admin/animal/delete/{id}', name: 'admin_animal_delete', methods: ['POST'])]
    public function deleteAnimal(Animal $animal, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($animal);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'animal a été supprimé avec succès.');
        }

        return $this->redirectToRoute('animal_manage');
    }
}