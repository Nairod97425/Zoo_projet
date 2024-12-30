<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Form\HabitatType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
// use App\Service\HabitatService;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

class HabitatController extends AbstractController
{
    private ManagerRegistry $doctrine;
    // private HabitatService $habitatService;
    private FileUploader $fileUploader;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, FileUploader $fileUploader)# HabitatService $habitatService
    {
        $this->doctrine = $doctrine;
        // $this->habitatService = $habitatService;
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
    }

    #[Route('/habitat/show/{id}', name: 'habitat_show', methods: ['GET'])]
    public function show(Habitat $habitat, AuthorizationCheckerInterface $authChecker): Response
    {
        // Vérification si l'habitat existe
        if ($habitat === null) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }
    
        // Récupération des animaux associés
        $animals = $habitat->getAnimals();
    
        // Vérification des droits d'accès
        $isUserLoggedIn = $this->getUser() !== null;
        $canEdit = $authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_EMPLOYE');
    
        return $this->render('habitat/liste.html.twig', [
            'habitats' => [$habitat], // Changed to plural
            'animals' => $animals,
            'isUserLoggedIn' => $isUserLoggedIn,
            'canEdit' => $canEdit,
        ]);
    }

    #[Route('/habitat', name: 'habitat_index', methods: ['GET'])]
    public function index(): Response
    {
        // $habitats = $this->habitatService->getAllHabitats();
        $habitats = $this->entityManager->getRepository(Habitat::class)->findAll();

        return $this->render('habitat/liste.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    #[Route('/habitat/new', name: 'habitat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $habitat = new Habitat("");
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->fileUploader->upload($file);
                        $filenames[] = $filename;
                    }
                }

                $habitat->setImages($filenames);
            }

            $animalName = $request->request->get('animal_name');
            if ($animalName) {
                $animal = new Animal();
                $animal->setName($animalName);
                $habitat->addAnimal($animal);
            }

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($habitat);
            $entityManager->flush();

            $this->addFlash('success', 'L\'habitat a été créé avec succès avec un animal associé.');
            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/habitat/edit/{id}', name: 'habitat_edit', methods: ['GET', 'POST'])]
    public function edit(Habitat $habitat, Request $request): Response
    {
        if ($habitat === null) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des fichiers d'images
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = $this->fileUploader->upload($file);
                        $filenames[] = $filename;
                    }
                }

                // Supprimer les anciennes images si nécessaire
                $existingImages = $habitat->getImages();
                foreach ($existingImages as $oldImage) {
                    $oldImagePath = $this->getParameter('uploads_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $habitat->setImages($filenames);
            }

            $this->doctrine->getManager()->flush();

            $this->addFlash('success', 'L\'habitat a été mis à jour avec succès.');
            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/edit.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    #[Route('/habitat/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitat $habitat, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Accès interdit.');
        }
        foreach ($habitat->getAnimals() as $animal) {
            $animal->setHabitat(null);
        }
       
            $entityManager->remove($habitat);
            $this->entityManager->flush();
            $this->addFlash('success', 'L\'habitat a été supprimé avec succès.');
        

        return $this->redirectToRoute('habitat_index');
    }
}
