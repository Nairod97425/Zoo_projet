<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Habitat;
use App\Form\AvisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AvisController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/avis', name: 'avis_index')]
    public function index(): Response
    {
        $avisList = $this->entityManager->getRepository(Avis::class)->findAll();

        return $this->render('avis/index.html.twig', [
            'avis' => $avisList,
        ]);
    }

    #[Route('/habitat/{id}/avis/new', name: 'avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Habitat $habitat): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Accès interdit.');
        }

        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUser($this->getUser());
            $avis->setHabitat($habitat);
            $avis->setStatut(true); // Par exemple, pour indiquer que l'avis est actif

            $this->entityManager->persist($avis);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');

            return $this->redirectToRoute('list_animal', ['habitatId' => $habitat->getId()]);
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    #[Route('/avis/edit/{id}', name: 'avis_edit')]
    public function edit(int $id, Request $request): Response
    {
        $avis = $this->entityManager->getRepository(Avis::class)->find($id);
        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/delete/{id}', name: 'avis_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $avis = $this->entityManager->getRepository(Avis::class)->find($id);
        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $this->entityManager->remove($avis);
        $this->entityManager->flush();

        return $this->redirectToRoute('avis_index');
    }
}
