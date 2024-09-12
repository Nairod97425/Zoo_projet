<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Habitat;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    #[Route('/habitat/{id}/avis', name: 'habitat_avis')]
    public function avis(Habitat $habitat, AvisRepository $avisRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère les avis approuvés pour cet habitat
        $avisList = $avisRepository->findBy([
            'habitat' => $habitat,
            'isApproved' => true
        ]);

        // Formulaire d'ajout d'avis
        $newAvis = new Avis();
        $form = $this->createForm(AvisType::class, $newAvis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAvis->setHabitat($habitat);
            $newAvis->setIsApproved(false);  // Avis doit être validé par l'admin
            $entityManager->persist($newAvis);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Votre avis a été soumis et est en attente d\'approbation.');

            return $this->redirectToRoute('habitat_avis', ['id' => $habitat->getId()]);
        }

        return $this->render('avis/index.html.twig', [
            'habitat' => $habitat,
            'avis' => $avisList,
            'form' => $form->createView(),
        ]);
    }
}
