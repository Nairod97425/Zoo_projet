<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'avis_index')]
    public function index(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findAll();

        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/new', name: 'avis_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/avis/edit/{id}', name: 'avis_edit')]
    public function edit(Avis $avis, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('avis_index');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/delete/{id}', name: 'avis_delete', methods: ['POST'])]
    public function delete(Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($avis);
        $entityManager->flush();

        return $this->redirectToRoute('avis_index');
    }
}
