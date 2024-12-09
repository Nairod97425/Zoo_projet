<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Form\HabitatType;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;


class HabitatController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/habitat/{id}', name: 'habitat_show', methods: ['GET'])]
    public function show(Habitat $habitat, AuthorizationCheckerInterface $authChecker): Response
    {
        // Récupérer les animaux associés à cet habitat
        $animals = $habitat->getAnimals();

        // Vérifier si l'utilisateur est connecté
        $isUserLoggedIn = $this->getUser() !== null;

         // Si l'utilisateur est connecté et a un rôle spécifique, vous pouvez afficher des options supplémentaires
         $canEdit = $authChecker->isGranted('ROLE_ADMIN') || $authChecker->isGranted('ROLE_EMPLOYE');
        

        // Rendre la vue avec les animaux de l'habitat
        return $this->render('habitat/show.html.twig', [
            'habitat' => $habitat,
            'animals' => $animals,
            'isUserLoggedIn' => $isUserLoggedIn,
            'canEdit' => $canEdit,  // Autoriser certains utilisateurs à éditer
        ]);
    }


    #[Route('/habitats', name: 'habitat_index', methods: ['GET'])]
    public function index(HabitatRepository $habitatRepository): Response
    {
        $habitats = $habitatRepository->findAll();
        return $this->render('habitat/liste.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    #[Route('/new', name: 'habitat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $habitat = new Habitat();
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier téléchargé (gestion des images)
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = uniqid() . '.' . $file->guessExtension();

                        // Déplacer le fichier
                        $file->move(
                            $this->getParameter('images_directory'),
                            $filename
                        );

                        $filenames[] = $filename;
                    }
                }

                // Enregistrer les noms des fichiers dans l'entité
                $habitat->setImages($filenames);
            }



            // Ajouter un animal à cet habitat
            $animalName = $request->request->get('animal_name'); // Récupérer le nom de l'animal via un paramètre POST
            if ($animalName) {
                $animal = new Animal();
                $animal->setName($animalName);
                $habitat->addAnimal($animal); // Lie l'animal à l'habitat
            }

            // Persister l'entité Habitat dans la base de données
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

    #[Route('/edit/{id}', name: 'habitat_edit', methods: ['GET', 'POST'])]
    public function edit(Habitat $habitat, Request $request): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des fichiers d'image
            $files = $form->get('images')->getData();

            if ($files) {
                $filenames = [];
                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $filename = uniqid() . '.' . $file->guessExtension();

                        // Déplacer le fichier dans le dossier approprié
                        $file->move(
                            $this->getParameter('images_directory'),
                            $filename
                        );

                        $filenames[] = $filename;
                    }
                }

                // Mettre à jour les images (facultatif : supprimer les anciennes)
                $existingImages = $habitat->getImages();
                foreach ($existingImages as $oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Supprimez l'ancienne image si elle existe
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

    #[Route('/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitat $habitat, EntityManagerInterface $entityManager): Response
    {
        foreach ($habitat->getAnimals() as $animal) {
            $animal->setHabitat(null);
        }

        $entityManager->remove($habitat);
        $entityManager->flush();

        return $this->redirectToRoute('habitat_index');
    }
}
