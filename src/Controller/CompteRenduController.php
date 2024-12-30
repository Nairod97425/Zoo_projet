<?php
// src/Controller/CompteRenduController.php

namespace App\Controller;

use App\Entity\CompteRendu;
use App\Form\CompteRenduType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteRenduController extends AbstractController
{
    #[Route('/compte-rendu/new', name: 'compte_rendu_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compteRendu = new CompteRendu();
        $form = $this->createForm(CompteRenduType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compteRendu);
            $entityManager->flush();

            return $this->redirectToRoute('compte_rendu_index');
        }

        return $this->render('compte_rendu/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte-rendu', name: 'compte_rendu_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $compteRendus = $entityManager->getRepository(CompteRendu::class)->findAll();

        return $this->render('compte_rendu/index.html.twig', [
            'compteRendus' => $compteRendus,
        ]);
    }

    #[Route('/compte-rendu/edit/{id}', name: 'compte_rendu_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $compteRendu = $entityManager->getRepository(CompteRendu::class)->find($id);
        if (!$compteRendu) {
            throw $this->createNotFoundException('Compte Rendu non trouvé');
        }

        $form = $this->createForm(CompteRenduType::class, $compteRendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('compte_rendu_index');
        }

        return $this->render('compte_rendu/edit.html.twig', [
            'form' => $form->createView(),
            'compteRendu' => $compteRendu,
        ]);
    }

    #[Route('/compte-rendu/delete/{id}', name: 'compte_rendu_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $compteRendu = $entityManager->getRepository(CompteRendu::class)->find($id);
        if (!$compteRendu) {
            throw $this->createNotFoundException('Compte Rendu non trouvé');
        }

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $compteRendu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($compteRendu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_rendu_index');
    }
}