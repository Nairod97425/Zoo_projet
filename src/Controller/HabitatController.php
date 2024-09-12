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

class HabitatController extends AbstractController
{
    #[Route('/habitats', name: 'habitat_index')]
    public function index(HabitatRepository $habitatRepository): Response
    {
        $habitats = $habitatRepository->findAll();

        return $this->render('habitat/index.html.twig', [
            'habitats' => $habitats,
        ]);
    }

    #[Route('/habitats/new', name: 'habitat_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $habitat = new Habitat();
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($habitat);
            $entityManager->flush();

            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/habitats/edit/{id}', name: 'habitat_edit')]
    public function edit(Habitat $habitat, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HabitatType::class, $habitat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('habitat_index');
        }

        return $this->render('habitat/edit.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    #[Route('/habitats/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitat $habitat, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($habitat);
        $entityManager->flush();

        return $this->redirectToRoute('habitat_index');
    }
}
