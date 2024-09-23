<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Form\HabitatType;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HabitatController extends AbstractController
{
    // Route pour afficher la liste des habitats
    #[Route('/habitats', name: 'habitat_index')]
    public function index(HabitatRepository $habitatRepository): Response
    {
        $habitats = $habitatRepository->findAll();

        return $this->render('habitat/liste.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    // Route pour créer un nouvel habitat
    #[Route('/habitats/new', name: 'habitat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HabitatRepository $habitatRepository): Response
    {
        $habitat = new Habitat();
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les fichiers d'images
            $images = $form->get('images')->getData();
            
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    // Définir le chemin de destination
                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/images'; // Exemple de dossier
                    
                    // Déplacez le fichier vers le dossier de destination
                    $image->move($destination, $image->getClientOriginalName());
        
                    // Ajouter le nom de l'image à l'entité
                    $habitat->addImage($image->getClientOriginalName());
                }
            }
        
            // Lien des animaux et sauvegarde
            foreach ($habitat->getAnimals() as $animal) {
                $animal->setHabitat($habitat);
            }
            $habitatRepository->save($habitat);

            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour éditer un habitat
    #[Route('/habitats/edit/{id}', name: 'habitat_edit')]
    public function edit(Habitat $habitat, Request $request, HabitatRepository $habitatRepository): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'image depuis le formulaire
            $image = $form->get('images')->getData();

            if ($image) {
                // Ajoute l'image (par exemple, en prenant le nom original)
                $habitat->addImage($image->getClientOriginalName());
            }

            // Utilise le repository pour sauvegarder les changements
            $habitatRepository->save($habitat);

            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/edit.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    // Route pour supprimer un habitat
    #[Route('/habitats/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitat $habitat, EntityManagerInterface $entityManager): Response
    {
        // Supprimer les animaux associés si nécessaire
        foreach ($habitat->getAnimals() as $animal) {
            $animal->setHabitat(null);
        }

        $entityManager->remove($habitat);
        $entityManager->flush();

        return $this->redirectToRoute('habitat_index');
    }
}
