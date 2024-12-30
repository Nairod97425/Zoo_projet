<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploaderAnimal;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAnimalController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FileUploaderAnimal $fileUploader;

    public function __construct(EntityManagerInterface $entityManager, FileUploaderAnimal $fileUploader)
    {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/animal/liste', name: 'list_animal_employer', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE')) {
            throw new AccessDeniedException('Accès interdit.');
        }

        // Récupérer tous les animaux
        $animals = $animalRepository->findAll();
        if (empty($animals)) {
            throw $this->createNotFoundException('Aucun animal trouvé');
        }

        return $this->render('admin_animal/liste.html.twig', [
            'animals' => $animals,
        ]);
    }

    #[Route('/animal_user/list_animals/{habitatId}', name: 'list_animal', methods: ['GET'])]
    public function show(int $habitatId, AnimalRepository $animalRepository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès interdit.');
        }

        // Retrieve the habitat
        $habitat = $this->entityManager->getRepository(Habitat::class)->find($habitatId);
        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        // Retrieve animals associated with the habitat
        $animals = $animalRepository->findBy(['habitat' => $habitat]);
        if (empty($animals)) {
            throw $this->createNotFoundException('Aucun animal trouvé pour cet habitat');
        }

        return $this->render('animal_user/list_animals.html.twig', [
            'animals' => $animals,
            'habitat' => $habitat,
        ]);
    }

    #[Route('/admin_animal/create', name: 'animal_new', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit.');
        }

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

            $habitatId = $request->request->get('habitat_id');
            if ($habitatId) {
                $habitat = $this->entityManager->getRepository(Habitat::class)->find($habitatId);
                if ($habitat) {
                    $animal->setHabitat($habitat);
                } else {
                    $this->addFlash('error', 'Habitat non trouvé.');
                }
            }

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
        if (!$this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit.');
        }

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
        if (!$this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit.');
        }

        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($animal);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'animal a été supprimé avec succès.');
        }

        return $this->redirectToRoute('animal_index');
    }
}
